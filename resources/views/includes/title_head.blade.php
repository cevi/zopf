@Section('page_header');
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="<?php echo e($dataType->icon); ?>"></i> <?php echo e($dataType->getTranslatedAttribute('display_name_plural')); ?>

        </h1>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add', app($dataType->model_name))): ?>
            <a href="<?php echo e(route('voyager.'.$dataType->slug.'.create')); ?>" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span><?php echo e(__('voyager::generic.add_new')); ?></span>
            </a>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', app($dataType->model_name))): ?>
            <?php echo $__env->make('voyager::partials.bulk-delete', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit', app($dataType->model_name))): ?>
            <?php if(isset($dataType->order_column) && isset($dataType->order_display_column)): ?>
                <a href="<?php echo e(route('voyager.'.$dataType->slug.'.order')); ?>" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span><?php echo e(__('voyager::bread.order')); ?></span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', app($dataType->model_name))): ?>
            <?php if($usesSoftDeletes): ?>
                <input type="checkbox" <?php if($showSoftDeleted): ?> checked <?php endif; ?> id="show_soft_deletes" data-toggle="toggle" data-on="<?php echo e(__('voyager::bread.soft_deletes_off')); ?>" data-off="<?php echo e(__('voyager::bread.soft_deletes_on')); ?>">
            <?php endif; ?>
        <?php endif; ?>
        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(method_exists($action, 'massAction')): ?>
                <?php echo $__env->make('voyager::bread.partials.actions', ['action' => $action, 'data' => null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('voyager::multilingual.language-selector', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
@endsection