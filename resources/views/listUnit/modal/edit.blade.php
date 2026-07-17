<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="editForm" method="POST">

                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5>Edit Unit</h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Vehicle</label>

                        <input
                            type="text"
                            class="form-control"
                            id="vhc"
                            readonly>
                    </div>

                    <div class="mb-3">

                        <label>Converter DC to DC</label>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="CONVERTER_DC_TO_DC"
                                id="converter1"
                                value="1">

                            <label class="form-check-label">
                                Ada
                            </label>

                        </div>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="CONVERTER_DC_TO_DC"
                                id="converter0"
                                value="0">

                            <label class="form-check-label">
                                Belum Ada
                            </label>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label>Status Enabled</label>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="STATUSENABLED"
                                id="status1"
                                value="1">

                            <label class="form-check-label">
                                True
                            </label>

                        </div>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="STATUSENABLED"
                                id="status0"
                                value="0">

                            <label class="form-check-label">
                                False
                            </label>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Update
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
