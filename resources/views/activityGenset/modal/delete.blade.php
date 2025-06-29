<div class="modal-demo" id="deleteGenset{{ $act->UUID }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <i class="mdi mdi-alert shake-alert" style="font-size: 64px; color: #dc3545;"></i>
                <div class="mt-4 pt-4">
                    <h4>Yakin menghapus data ini?</h4>
                    <p class="text-muted"> Data yang dihapus tidak ditampilkan lagi!!!</p>
                    <!-- Toogle to second dialog -->
                    <a href="{{ route('activityGenset.delete', $act->UUID) }}" type="button"  class="btn btn-warning">Hapus</a>
                </div>
            </div>
        </div>
    </div>
</div>
