import {AfterViewInit, Component, ElementRef, OnInit, ViewChild} from "@angular/core";
import {ApiService} from "../api.service";
import {ActivatedRoute, Router} from "@angular/router";
import {SessionStorageService} from "ngx-webstorage";
import {ForkJoinObservable} from "rxjs/observable/ForkJoinObservable";
import * as moment from "moment";

declare const $: any;

@Component({
  selector: 'app-pre-application-process',
  templateUrl: './pre-application-process.component.html',
  styleUrls: ['./pre-application-process.component.css']
})
export class PreApplicationProcessComponent implements OnInit, AfterViewInit {
  @ViewChild('instruction_view') instructionView: ElementRef;
  @ViewChild('extraCondition') extraCondition: ElementRef;
  @ViewChild('categoryHeader') categoryHeader: ElementRef;
  @ViewChild('categoryConditions') categoryConditions: ElementRef;
  applicationRules;
  message = 'Loading rules....';
  Objects = Object;
  circularID;
  ansar_id;
  isAgreed = false;
  isValid = false;
  mobildNoSelf;
  selectedDistrict = '';
  error_message: string = null;
  isExistsAnsarCategory = false;
  circular;
  category;
  instruction = '';
  circularRules;
  mega_error_message: string = null;
  quotaStatus = false;
  quotaList = [];
  districts = {};
  quota_type = {
    'son_of_freedom_fighter': 'মুক্তিযোদ্ধার সন্তান',
    'grand_son_of_freedom_fighter': 'মুক্তিযোদ্ধার নাতি-নাতনি ',
    'member_of_ansar_or_vdp': 'আনসার ভিডিপি সদস্য',
    'orphan': 'এতিম',
    'physically_disabled': 'শারীরিক প্রতিবন্ধী',
    'tribe': 'উপজাতি'
  };

  constructor(private api: ApiService, private route: ActivatedRoute, private sessionStorage: SessionStorageService, private router: Router) {
  }

  ngOnInit() {
	  
    this.message = 'Loading rules....';
    this.route.queryParams.subscribe(data => {
      if (data) {
        if (!this.sessionStorage.retrieve('show_message')) {
          this.sessionStorage.store('show_message', 1);
          this.error_message = data.error_message;
        } else {
          this.error_message = null;
        }
      } else {
        this.error_message = null;
      }
    });
  }

  submitForm(event, value) {
    event.preventDefault();
    const paymentForm = $('#paymentForm');
    const mobile = paymentForm.find('*[name="mobile_no_self"]').val().split(/^(\+?88)?/).pop();
    const data = {
      mobile_no_self: mobile,
      job_circular_id: this.circularID,
      status: {'ne': 'initial'}
    };
    if (this.isExistsAnsarCategory) {
      data['ansar_id'] = this.ansar_id;
    }
    this.api.validateUniqueness(data).subscribe(status => {
      if (!status) {
        alert('mobile no already exists');
        return;
      }
      this.sessionStorage.clear('show_message');
      paymentForm.find('*[name="paymentOption"]').val(value);
      paymentForm.submit();
    });
  }

  checkMobileNo(newVal) {
     
	  //console.log(newVal); 
	  console.log(this.mobildNoSelf);
    if (this.isExistsAnsarCategory && this.mobildNoSelf.length > 10) {
	  this.checkAnsarIdAndMobile();
      return;
    }
	
	console.log(this.mobildNoSelf);
	
	
	//alert(this.selectedDistrict);
	
	/* if (!this.selectedDistrict || this.selectedDistrict == '') {
      this.isValid = false;
	  this.mega_error_message = 'জেলা সিলেক্ট করুন';
      return;
    }else{
		this.isValid = true;
		this.mega_error_message = '';
		
	}
	 */
	
	
    this.isValid = /^(\\+?88)?[0-9]{11}$/.test(this.mobildNoSelf);
    if (!this.isValid) {
      this.mega_error_message = 'আপনার মোবাইল নম্বর সঠিক নয়।';
    } else {
      this.mega_error_message = '';
    }
  }

  checkAnsarIdAndMobile() {
	  alert('rintu');
    let ansarID = '';
    let applicantData;
    let ansar;
	let validityData;
	
    if (!this.ansar_id || !this.mobildNoSelf || this.ansar_id == '' || this.mobildNoSelf == '') {
      this.isValid = false;
      return;
    
		}
    if (this.ansar_id.length > 5) {
      ansarID = this.ansar_id.slice(-5);
    } else {
      ansarID = this.ansar_id;
    }

    ForkJoinObservable.create([
      this.api.getApplicantByIdAndMobile({job_circular_id: this.circularID, mobile_no_self: this.mobildNoSelf}),
      this.api.getAnsarByAnsarId(parseInt(ansarID)),
	  this.api.validityCheckByAnsarId(parseInt(ansarID))
    ]).subscribe(
      (data) => {
		
        applicantData = data[0];
        ansar = data[1];
		validityData = data[2];
		if(validityData.status == false){
			          this.isValid = false;
					  this.mega_error_message = validityData['message'];
                      return;
		}
        this.isValid = (this.mobildNoSelf == ansar['mobile_no_self'] && this.ansar_id == ansar['ansar_id'] &&
        applicantData.length <= 0);
		//console.log(this.circularRules['constraint']);
		const minAgeRule = this.circularRules['constraint'][0]['age']['min'];
		const maxAgeRule = this.circularRules['constraint'][0]['age']['max'];
		const minDateRule = this.circularRules['constraint'][0]['age']['minDate'];
		const maxDateRule = this.circularRules['constraint'][0]['age']['maxDate'];
        
		const ageValid = this.validateAge(minAgeRule, maxAgeRule,
          minDateRule, maxDateRule, ansar['data_of_birth']);
        console.log('isValid check');
		console.log(this.mobildNoSelf);
		console.log(this.ansar_id);
		console.log(ansar);
		console.log(applicantData);
        console.log(this.isValid);

        if (!this.isValid) {
          this.mega_error_message = 'আপনার মোবাইল নম্বর ইতিমধ্যে ব্যবহৃত হয়েছে অথবা আপনার ব্যক্তিগত তথ্য সার্কুলার অনুযায়ী সঠিক নয়।';
        } else if (!ageValid) {
          this.isValid = false;
          this.mega_error_message = 'আপনার বয়স সার্কুলার অনুযায়ী সামঞ্জস্যপূর্ণ নয়। আপনার বর্তমান বয়স ' + this.getAge(ansar['data_of_birth']);
        } else {
          this.mega_error_message = '';
        }
      },
      (error) => {
        console.log(error);
      }
    );
  }

  type = (q) => {
    return typeof q;
  }

  ngAfterViewInit(): void {
    this.route.params.subscribe(data => {
      this.circularID = data['id'];
      // ========~~
      this.api.getCircularRules(this.circularID).subscribe((rules) => {
		 // console.log(rules['constraint'][0]['age']);
        this.applicationRules = rules['constraint'];
		this.circularRules = rules;
		
        this.quotaList = rules['quota'];
        this.quotaStatus = rules['quotaStatus'];
      });
	  
	   // get allowed districts with circular id  
	   this.api.getCircularDistricts(this.circularID).subscribe((districts) => {      
		this.districts = districts;
		//console.log(districts);
	   }, err => {
        console.log(err);
      });
	  
	  
      this.api.getCircularById(this.circularID).subscribe((circular) => {
        this.circular = circular;
        if (circular.hasOwnProperty('job_category_id') && circular['job_category_id']) {
          this.api.getJobCategoryById(circular['job_category_id']).subscribe((category) => {
            this.category = category;
            //this.categoryHeader.nativeElement.innerHTML = category['category_header'];
            this.categoryConditions.nativeElement.innerHTML = category['category_conditions'];
			
            if (category['category_type'] == 'other' || circular['terms_and_conditions']) this.extraCondition.nativeElement.innerHTML = circular['terms_and_conditions'];
            if (category.hasOwnProperty('category_type') && (category['category_type'] === 'apc_training' || category['category_type'] === 'pc_training')) {
              this.isExistsAnsarCategory = true;
            }
          });
        }
      });
      // this.api.getCircularRules(data['id']).subscribe(result => {
      //   this.applicationRules = result['constraint'];
      //   this.quotaStatus = result['quotaStatus'];
      //   this.quotaList = result['quota'];
      // }, err => {
      //   this.message = err.errors['message'];
      // });
      this.api.getApplicationInstruction().subscribe(success => {
        // console.log(success['instruction']);
        // document.getElementById('instruction').innerHTML = success['instruction'];
        // // this.instructionView.nativeElement.innerHTML = success['instruction'];
      }, err => {
        console.log(err);
      });
    });
  }

  getQuotaName = (id) => {
    const q = this.quotaList.filter((v) => {
      return v.id == id;
    });
    if (q && q.length > 0)return q[0]['quota_name_bng'];
    // else return "-----";
  }

  validateAge(minAge, maxAge, minDate, maxDate, data) {
    moment.locale('en');
    let currentMinAge = moment(minDate, 'DD-MMM-YYYY').diff(moment(data), 'years', true);
    let currentMaxAge = moment(maxDate, 'DD-MMM-YYYY').diff(moment(data), 'years', true);
    console.log('==>' + currentMinAge + ' ' + currentMaxAge + ' ' + data + ' ' + minDate + ' ' + maxDate);

    if (currentMinAge < 0 && currentMaxAge < 0) {
      currentMinAge = moment(minDate, 'DD-MMM-YYYY').diff(moment(data, 'YYYY-MM-DD'), 'years', true);
      currentMaxAge = moment(maxDate, 'DD-MMM-YYYY').diff(moment(data, 'YYYY-MM-DD'), 'years', true);
    }
    console.log(minDate, minAge, currentMinAge, maxDate, maxAge, currentMaxAge);
    const valid = (currentMinAge >= minAge && currentMaxAge <= maxAge) && currentMinAge > 0 && currentMaxAge > 0;
    if (!valid) {
      return false;
    }
    return true;
  }

  getAge(date) {
    return parseInt(moment().diff(moment(date, 'YYYY-MM-DD'), 'years', true) + '');
  }
}
