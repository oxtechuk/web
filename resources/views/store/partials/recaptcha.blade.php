@if($siteKey = config('services.recaptcha.site_key'))
    <input type="hidden" name="g-recaptcha-response" class="g-recaptcha-token-field" value="">
    @error('g-recaptcha-response')
        <div class="text-danger mt-1 font-semibold text-sm" style="color: #dc2626; font-size: 0.85rem; margin-top: 6px;">{{ $message }}</div>
    @enderror

    @once
        <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ $siteKey }}', {action: 'submit'}).then(function(token) {
                            document.querySelectorAll('.g-recaptcha-token-field').forEach(function(input) {
                                input.value = token;
                            });
                        });
                    });
                }
            });
        </script>
    @endonce
@endif
