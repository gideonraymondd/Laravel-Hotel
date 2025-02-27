$(function () {
    const currentRoute = window.location.pathname;
    if (!currentRoute.split("/").includes("room")) return;

    const datatable = $("#room-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/room`,
            type: "GET",
            data: function (d) {
                d.status = $("#status").val();
                d.type = $("#type").val();
            },
            error: function (xhr, status, error) {},
        },
        columns: [
            { name: "number", data: "number" },
            { name: "type", data: "type" },
            { name: "capacity", data: "capacity" },
            {
                name: "price",
                data: "price",
                render: function (price) {
                    return `<div>${new Intl.NumberFormat().format(price)}</div>`;
                },
            },
            { name: "status", data: "status" },
            {
                name: "id",
                data: "id",
                render: function (roomId) {
                    return `
                        <div class="d-flex justify-content-center align-items-center gap-2 p-2">
                            <button class="btn btn-light btn-sm rounded shadow-sm border"
                                data-action="edit-room" data-room-id="${roomId}"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit room">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-light btn-sm rounded shadow-sm border delete-room"
                                data-room-id="${roomId}" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Delete room">
                                <i class="fas fa-trash-alt"></i>
                            </button>

                            <a class="btn btn-light btn-sm rounded shadow-sm border"
                                href="/room/${roomId}"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Room detail">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </div>
                    `;
                },
            },
        ],
    });

    const modal = new bootstrap.Modal($("#main-modal"), {
        backdrop: true,
        keyboard: true,
        focus: true,
    });

    // **Event untuk Menghapus Room dengan SweetAlert**
    $(document).on("click", ".delete-room", function () {
        const roomId = $(this).data("room-id");

        Swal.fire({
            title: "Are you sure?",
            text: "Room will be deleted, and you won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await $.ajax({
                        url: `/room/${roomId}`,
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    datatable.ajax.reload();
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: error.responseJSON?.message || "Something went wrong!",
                    });
                }
            }
        });
    });

    // **Event untuk Tambah Room**
    $(document).on("click", "#add-button", async function () {
        modal.show();
        $("#main-modal .modal-body").html(`Fetching data...`);
        const response = await $.get(`/room/create`);
        if (!response) return;
        $("#main-modal .modal-title").text("Create new room");
        $("#main-modal .modal-body").html(response.view);
        $(".select2").select2();
    });

    // **Event untuk Simpan Room**
    $(document).on("click", "#btn-modal-save", function () {
        $("#form-save-room").submit();
    });

    $(document).on("submit", "#form-save-room", async function (e) {
        e.preventDefault();
        $("#btn-modal-save").attr("disabled", true);

        try {
            const response = await $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                method: $(this).attr("method"),
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            Swal.fire({
                position: "center",
                icon: "success",
                title: response.message,
                showConfirmButton: false,
                timer: 1500,
            });

            modal.hide();
            datatable.ajax.reload();
        } catch (e) {
            if (e.status === 422) {
                let errorMessages = e.responseJSON.errors;
                let allErrors = '';

                // Gabungkan semua pesan error dalam satu string
                for (let field in errorMessages) {
                    errorMessages[field].forEach(function(msg) {
                        allErrors += msg + '\n'; // Menambahkan setiap pesan ke string
                    });
                }

                // Tampilkan semua pesan error dalam Swal modal
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: allErrors,  // Menampilkan semua pesan error
                });
            }

        } finally {
            $("#btn-modal-save").attr("disabled", false);
        }
    });

    // **Event untuk Edit Room**
    $(document).on("click", '[data-action="edit-room"]', async function () {
        modal.show();
        $("#main-modal .modal-body").html(`Fetching data...`);
        const roomId = $(this).data("room-id");
        const response = await $.get(`/room/${roomId}/edit`);
        if (!response) return;
        $("#main-modal .modal-title").text("Edit room");
        $("#main-modal .modal-body").html(response.view);
        $(".select2").select2();
    });

    // **Event Filter Data**
    $(document).on("change", "#status, #type", function () {
        datatable.ajax.reload();
    });
});
