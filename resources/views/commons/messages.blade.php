@if(Session::has('success'))
  <div class="alert alert-success" role="alert">{{ session('success') }}</div>
@endif

@if(Session::has('failed'))
  <div class="alert alert-danger" role="alert">{{ session('failed') }}</div>
@endif