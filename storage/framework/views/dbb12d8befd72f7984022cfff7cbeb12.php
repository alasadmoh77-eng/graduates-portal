

<?php $__env->startSection('title', __('app.register')); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-lg position-relative">
            <a href="/" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" title="<?php echo e(app()->getLocale() == 'ar' ? 'الرجوع للرئيسية' : 'Back to Home'); ?>"></a>
            <div class="text-center mb-4">
                <h2 class="fw-bold"><?php echo e(__('app.register')); ?></h2>
                <p class="text-muted"><?php echo e(__('app.register_subtitle')); ?></p>
            </div>
            
            <form action="<?php echo e(route('register')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.name')); ?></label>
                        <input type="text" name="name" id="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>" required readonly placeholder="<?php echo e(app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically'); ?>">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.email')); ?></label>
                        <input type="email" name="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email')); ?>" required>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.university_id')); ?></label>
                        <input type="text" name="university_id" id="university_id" class="form-control <?php $__errorArgs = ['university_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('university_id')); ?>" required placeholder="e.g. 2020-001" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <?php $__errorArgs = ['university_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div id="university-feedback" class="invalid-feedback d-block" style="display: none;"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.phone')); ?></label>
                        <input type="tel" name="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone')); ?>" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.faculty')); ?></label>
                        <input type="text" id="faculty_name" class="form-control" readonly placeholder="<?php echo e(app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.major')); ?></label>
                        <input type="text" id="major_name" class="form-control" readonly placeholder="<?php echo e(app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.graduation_year')); ?></label>
                        <input type="text" id="graduation_year" class="form-control" readonly placeholder="<?php echo e(app()->getLocale() == 'ar' ? 'سيتم التعبئة تلقائياً عند إدخال الرقم الجامعي' : 'Will be filled automatically'); ?>">
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.password')); ?></label>
                        <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><?php echo e(__('app.confirm_password')); ?></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                    <?php echo e(__('app.register')); ?>

                </button>
            </form>
            
            <div class="text-center mt-4 pt-2 border-top">
                <p class="mb-0 text-muted">
                    لديك حساب بالفعل؟
                    <a href="<?php echo e(route('login')); ?>" class="text-primary fw-bold text-decoration-none">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    $(document).ready(function() {
        var universityIdInput = $('#university_id');
        var nameInput = $('#name');
        var emailInput = $('#email');
        var facultyInput = $('#faculty_name');
        var majorInput = $('#major_name');
        var gradYearInput = $('#graduation_year');
        var feedbackDiv = $('#university-feedback');

        function checkGraduate() {
            var val = universityIdInput.val().trim();
            if (val.length === 0) {
                nameInput.val('');
                emailInput.val('').removeAttr('readonly');
                facultyInput.val('');
                majorInput.val('');
                gradYearInput.val('');
                universityIdInput.removeClass('is-valid is-invalid');
                feedbackDiv.text('').hide();
                return;
            }

            // Show checking indicator
            feedbackDiv.removeClass('invalid-feedback text-success').addClass('text-info').text('جاري التحقق من سجلات الجامعة...').show();

            $.ajax({
                url: '/api/check-graduate/' + encodeURIComponent(val),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        universityIdInput.removeClass('is-invalid').addClass('is-valid');
                        feedbackDiv.removeClass('invalid-feedback text-info').addClass('text-success').text('تم التحقق من الرقم الجامعي بنجاح: ' + response.graduate.name).show();
                        
                        nameInput.val(response.graduate.name);
                        facultyInput.val(response.graduate.college || '');
                        majorInput.val(response.graduate.major);
                        gradYearInput.val(response.graduate.graduation_year);

                        if (response.graduate.email && response.graduate.email.trim() !== '') {
                            emailInput.val(response.graduate.email).attr('readonly', true);
                        } else {
                            emailInput.val('').removeAttr('readonly');
                        }
                    } else {
                        universityIdInput.removeClass('is-valid').addClass('is-invalid');
                        feedbackDiv.removeClass('text-success text-info').addClass('invalid-feedback').text(response.message).show();
                        
                        nameInput.val('');
                        emailInput.val('').removeAttr('readonly');
                        facultyInput.val('');
                        majorInput.val('');
                        gradYearInput.val('');
                    }
                },
                error: function() {
                    feedbackDiv.removeClass('text-success text-info').addClass('invalid-feedback').text('حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة لاحقاً.').show();
                }
            });
        }

        // Trigger check on blur and change
        universityIdInput.on('blur change', checkGraduate);

        // Also trigger check if there is an initial value
        if (universityIdInput.val().trim().length > 0) {
            checkGraduate();
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\RTX\Desktop\myproject\ملفات المشروع\المعدلهgraduates-portal3.22\resources\views/auth/register.blade.php ENDPATH**/ ?>