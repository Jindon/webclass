Welcome {{ auth()->user()->name }}

<form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" >
    @csrf
    <button type="submit">Logout</button>
</form>
