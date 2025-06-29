<div class="modal-demo" id="deleteBarangKeluar{{ $brm->UUID }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <lord-icon
                    src="{{ asset('tdrtiskw.json') }}"
                    trigger="loop"
                    colors="primary:#f7b84b,secondary:#405189"
                    style="width:130px;height:130px">
                </lord-icon>
                <div class="mt-4 pt-4">
                    <h4>Yakin menghapus data ini?</h4>
                    <p class="text-muted"> Data yang dihapus tidak ditampilkan lagi!!!</p>
                    <!-- Toogle to second dialog -->
                    <a href="{{ route('barangKeluar.delete', $brm->UUID) }}" type="button"  class="btn btn-warning">Hapus</a>
                </div>
            </div>
        </div>
    </div>
</div>
