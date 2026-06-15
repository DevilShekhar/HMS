@extends('layouts.app')

@section('content')

<section class="section premium-dashboard">
    <div class="premium-page-head">
        <div class="premium-page-title">
            <span class="mini-badge">Role Management</span>
            <h2>Manage Permissions</h2>
            <p>Role: <b>{{ $role->name }}</b></p>
        </div>
    </div>
</section>

<section class="section premium-dashboard pt-0">

<form method="POST"
      action="{{ route('roles.permissions.update', ['role' => $role->id]) }}">
    @csrf

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
        <input type="text" id="searchPermission"
               placeholder="Search permissions..."
               style="padding:10px 14px;width:300px;border:1px solid #ddd;border-radius:10px;">

        <button type="button" id="selectAllBtn"
                style="padding:10px 18px;border:none;border-radius:10px;background:#4f46e5;color:#fff;">
            Select All
        </button>
    </div>

    <div class="row">

        @foreach ($permissions as $groupName => $groupPermissions)

            @if($groupPermissions->count())

            <div class="col-6 mb-4">

                <div style="background:#fff;border-radius:14px;overflow:hidden;border:1px solid #eee;">

                    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #eee;">
                        <div>
                            <strong>{{ $groupName }}</strong><br>
                            <small style="color:#666;">{{ $groupPermissions->count() }} permissions</small>
                        </div>

                        <button type="button"
                                class="group-select-btn"
                                style="padding:6px 12px;border:1px solid #ddd;border-radius:8px;background:#fff;">
                            Select Group
                        </button>
                    </div>

                    <div class="row" style="padding:15px;">

                        @foreach ($groupPermissions as $permission)

                        <div class="col-xl-3 col-lg-4 col-md-6 mb-2 permission-wrapper">

                            <label class="permission-item">

                                <input type="checkbox"
                                       class="permission-checkbox"
                                       name="permissions[]"
                                       value="{{ $permission->name }}"
                                       {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>

                                <span class="permission-label">
                                    {{ ucwords(str_replace('_',' ', $permission->name)) }}
                                </span>

                            </label>

                        </div>

                        @endforeach

                    </div>

                </div>
            </div>

            @endif
        @endforeach

    </div>

    <div style="position:sticky;bottom:15px;text-align:right;">
        <button type="submit"
                style="padding:12px 26px;border:none;border-radius:12px;background:#16a34a;color:#fff;font-weight:600;">
            Save Permissions
        </button>
    </div>

</form>

</section>

<style>

.permission-item{
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border:1px solid #e5e7eb;
    border-radius:10px;
    cursor:pointer;
    transition:.2s ease;
    background:#fff;
    min-height:48px;
}

.permission-item:hover{
    border-color:#cbd5e1;
}

.permission-checkbox{
    width:15px;
    height:15px;
    cursor:pointer;
}

.permission-item.active{
    background:#ecfdf3;
    border-color:#86efac;
}

.permission-item.active .permission-label{
    color:#166534;
    font-weight:600;
}

.permission-label{
    font-size:13px;
    color:#374151;
}

</style>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const updateUI = () => {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            const item = cb.closest('.permission-item');
            cb.checked ? item.classList.add('active') : item.classList.remove('active');
        });
    };

    updateUI();

    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    document.querySelectorAll('.group-select-btn').forEach(btn => {
        btn.addEventListener('click', function () {

            const box = this.closest('div').parentElement;
            const checkboxes = box.querySelectorAll('.permission-checkbox');

            const allChecked = [...checkboxes].every(c => c.checked);

            checkboxes.forEach(c => c.checked = !allChecked);

            updateUI();
        });
    });

    document.getElementById('selectAllBtn').addEventListener('click', function () {

        const all = document.querySelectorAll('.permission-checkbox');
        const allChecked = [...all].every(c => c.checked);

        all.forEach(c => c.checked = !allChecked);

        this.innerText = allChecked ? 'Select All' : 'Unselect All';

        updateUI();
    });

    document.getElementById('searchPermission').addEventListener('input', function () {

        const value = this.value.toLowerCase();

        document.querySelectorAll('.permission-wrapper').forEach(el => {

            const text = el.innerText.toLowerCase();

            el.style.display = text.includes(value) ? '' : 'none';

        });

    });

});

</script>

@endsection
