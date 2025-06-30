@error ('avatar')
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-20 me-1"></iconify-icon>
        <div class="lh-1">
            <strong>Info - </strong> {{ $message }} !
        </div>
    </div>
@enderror
@error ('current_password')
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-20 me-1"></iconify-icon>
        <div class="lh-1">
            <strong>Info - </strong> {{ $message }} !
        </div>
    </div>
@enderror
@error ('new_password')
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-20 me-1"></iconify-icon>
        <div class="lh-1">
            <strong>Info - </strong> {{ $message }} !
        </div>
    </div>
@enderror
