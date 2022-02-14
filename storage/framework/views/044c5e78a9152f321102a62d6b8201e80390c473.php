<table class="table table-bordered table-striped">
    <caption>
        <div class="row">
            <div class="col-sm-8">Total : <?php echo e($data->total()); ?></div>
            <div class="col-sm-4">
                <div class="input-group">
                    <input type="text" ng-model="q" class="form-control" placeholder="search by mem. ID">
                    <span class="input-group-addon">
                        <a href="#" onclick="return false" ng-click="loadData(undefined,q)">
                            <i class="fa fa-search"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </caption>
    <tr>
        <th>#</th>
        <th>Memorandum no.</th>
        <th>Memorandum Date</th>
		<?php if((auth()->user()->type==11) && $type=='EMBODIMENT'): ?>
		<?php else: ?>
          <th>Unit</th>
		<?php endif; ?>
        <th>Action</th>
    </tr>
    <?php $i = (intVal($data->currentPage()-1)*$data->perPage())+1; ?>
    <?php foreach($data as $mem): ?>
        <tr>
            <td><?php echo e($i++); ?></td>
            <td>
                <?php echo e($mem->memorandum_id); ?>

            </td>
            <td><?php echo e($mem->mem_date?($mem->mem_date):'n/a'); ?></td>
			<?php if((auth()->user()->type==11) && $type=='EMBODIMENT'): ?>
			
			<?php else: ?>

            <td>
                <?php if(auth()->user()->type!=22): ?>
                    <select class="form-control" ng-model="unit_mem[<?php echo e($i-1); ?>]" name="unit_list">
                        <option value="">--<?php echo app('translator')->get('title.unit'); ?>--</option>
                        <?php foreach($units as $u): ?>
                            <option value="<?php echo e($u->id); ?>"><?php echo e($u->unit_name_bng); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <div>
                        <?php echo e(auth()->user()->district?auth()->user()->district->unit_name_eng:''); ?>


                    </div>
                <?php endif; ?>
            </td>
			<?php endif; ?>
            <td>
                <?php echo Form::open(['route'=>'print_letter','target'=>'_blank']); ?>

                <?php echo Form::hidden('option','memorandumNo'); ?>

                <?php echo Form::hidden('id',$mem->memorandum_id); ?>

                <?php echo Form::hidden('type',$type); ?>

				<?php if((auth()->user()->type==11) && $type=='EMBODIMENT'): ?>
				<?php echo Form::hidden('unit','0'); ?>

				<?php else: ?>
                <?php echo Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'[[unit_mem['.($i-1).'] ]]',['id'=>'unit_mem']); ?>

				<?php endif; ?>
                <button class="btn btn-primary">Generate Letter</button>
                <?php echo Form::close(); ?>

            </td>
        </tr>
    <?php endforeach; ?>
    <?php if($i==1): ?>
        <tr>
            <td class="warning" colspan="5">No Memorandum no. available</td>
        </tr>
    <?php endif; ?>
</table>
<div class="pull-right" paginate ref="loadData(url)">
    <?php echo $data->render(); ?>

</div>
<?php /*
<script>
    $(document).ready(function () {
        $('select[name="unit_list"]').on('change',function (event) {
            var i = this;
            $("#unit_mem").val(this.value);
        })
    })
</script>*/ ?>
