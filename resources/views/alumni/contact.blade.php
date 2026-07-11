@extends('layouts.app')

@section('title', __('app.contact_us'))

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="fw-bold" style="color: var(--primary-blue);">
                    {{ app()->getLocale() == 'ar' ? 'اتصل بشؤون الخريجين' : 'Contact Alumni Affairs' }}
                </h1>
                <p class="text-muted fs-6">
                    {{ app()->getLocale() == 'ar' ? 'يسرنا استفساراتكم واقتراحاتكم' : 'We welcome your inquiries and suggestions' }}
                </p>
            </div>

            <div class="row g-4">
                <!-- Contact Form -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="fw-bold mb-4" style="color: var(--primary-blue);">
                                <i class="fas fa-pen-alt me-2"></i>
                                {{ app()->getLocale() == 'ar' ? 'أرسل رسالة' : 'Send a Message' }}
                            </h4>

                            <form action="{{ route('alumni.contact.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold text-secondary">
                                        {{ app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Full Name' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold text-secondary">
                                        {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" id="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label fw-bold text-secondary">
                                        {{ app()->getLocale() == 'ar' ? 'الموضوع' : 'Subject' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="subject" id="subject"
                                        class="form-control form-control-lg @error('subject') is-invalid @enderror"
                                        value="{{ old('subject') }}" required>
                                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label fw-bold text-secondary">
                                        {{ app()->getLocale() == 'ar' ? 'الرسالة' : 'Message' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="message" id="message" rows="5"
                                        class="form-control form-control-lg @error('message') is-invalid @enderror"
                                        required>{{ old('message') }}</textarea>
                                    @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <button type="submit" class="btn btn-lg btn-primary rounded-pill px-5 fw-bold w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    {{ app()->getLocale() == 'ar' ? 'إرسال الرسالة' : 'Send Message' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100"
                        style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
                        <div class="card-body p-4 p-md-5 text-white">
                            <h4 class="fw-bold mb-4">
                                <i class="fas fa-address-card me-2"></i>
                                {{ app()->getLocale() == 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}
                            </h4>

                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-white bg-opacity-20 rounded-3 p-3">
                                    <i class="fas fa-map-marker-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ app()->getLocale() == 'ar' ? 'العنوان' : 'Address' }}</h6>
                                    <p class="mb-0 small opacity-85">
                                        {{ app()->getLocale() == 'ar' ? 'الجمهورية اليمنية، مأرب، الحرم الجامعي - مبنى شؤون الخريجين' : 'University Campus, Marib, Republic of Yemen - Alumni Affairs Building' }}
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-white bg-opacity-20 rounded-3 p-3">
                                    <i class="fas fa-phone fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ app()->getLocale() == 'ar' ? 'الهاتف' : 'Phone' }}</h6>
                                    <p class="mb-0 small opacity-85"><a href="tel:+9676302008"
                                            class="text-white text-decoration-none">+9676302008</a></p>
                                    <p class="mb-0 small opacity-85"><a href="tel:+9676301274"
                                            class="text-white text-decoration-none">+9676301274</a></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-white bg-opacity-20 rounded-3 p-3">
                                    <i class="fab fa-whatsapp fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ app()->getLocale() == 'ar' ? 'واتساب' : 'WhatsApp' }}</h6>
                                    <p class="mb-0 small opacity-85"><a href="https://wa.me/967780641221" target="_blank"
                                            rel="noopener" class="text-white text-decoration-none">780641221</a></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-white bg-opacity-20 rounded-3 p-3">
                                    <i class="fas fa-envelope fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">
                                        {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                                    </h6>
                                    <p class="mb-0 small opacity-85"><a href="mailto:INFO@USR.AC"
                                            class="text-white text-decoration-none">INFO@USR.AC</a></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-white bg-opacity-20 rounded-3 p-3">
                                    <i class="fas fa-clock fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">
                                        {{ app()->getLocale() == 'ar' ? 'ساعات العمل' : 'Working Hours' }}
                                    </h6>
                                    <p class="mb-0 small opacity-85">
                                        {{ app()->getLocale() == 'ar' ? 'السبت - الخميس: 8:00 ص - 2:00 م' : 'Saturady - Thursday: 8:00 AM - 2:00 PM' }}
                                    </p>
                                </div>
                            </div>

                            <hr class="border-light opacity-25 my-4">

                            <div class="text-center">
                                <p class="mb-2 small opacity-75">
                                    {{ app()->getLocale() == 'ar' ? 'تابعنا على وسائل التواصل' : 'Follow us on social media' }}
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="https://www.facebook.com/wsa.usr?locale=ar_AR" target="_blank" rel="noopener"
                                        class="btn btn-outline-light btn-sm rounded-circle p-2"
                                        style="width: 38px; height: 38px;"><i class="fab fa-facebook-f"></i></a>
                                    <a href="https://www.youtube.com/channel/UCZF1gnR_VW1GI1epjqym5xg" target="_blank"
                                        rel="noopener" class="btn btn-outline-light btn-sm rounded-circle p-2"
                                        style="width: 38px; height: 38px;"><i class="fab fa-youtube"></i></a>
                                    <a href="https://wa.me/967780641221" target="_blank" rel="noopener"
                                        class="btn btn-outline-light btn-sm rounded-circle p-2"
                                        style="width: 38px; height: 38px;"><i class="fab fa-whatsapp"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection