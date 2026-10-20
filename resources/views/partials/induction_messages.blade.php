@if(session('success'))
    <div role="status" style="background: #27ae60; color: #fff; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; line-height: 1.5;"><i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div role="alert" style="background: #c0392b; color: #fff; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; line-height: 1.5;"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
    <div role="alert" style="background: #c0392b; color: #fff; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; line-height: 1.5;"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ $errors->first() }}</div>
@endif
