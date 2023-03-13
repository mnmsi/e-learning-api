$(document).ready(function ($) {
    //Nav Active Start
    let activeLink = $('.navbar-nav li a[href="' + location.href + '"]');
    activeLink.addClass('active')
    activeLink.closest('ul').addClass('show')
    activeLink.parents('.nav-item').children('a.nav-link').addClass('active')
    //Nav Active End
});

function fnDelete(url, rowId) {

    Swal.fire({
        title: 'Are you sure?',
        text: "If yes, than it can't be restore!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: "linear-gradient(310deg, #7928ca, #ff0080)",
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (!result.isConfirmed) {
            return false;
        }

        $.ajax({
            type: "get",
            url: url,
            dataType: "json",
            success: function (response) {

                var row = $('table tbody tr[cellData=' + '\'' + rowId + '\'' + ']');

                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success...',
                        timer: 2000
                    });
                    row.remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg.join('&'),
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.responseJSON.msg.join('&'),
                });
            }
        });
    })
}

function fnShow(id) {
    $('#' + id).toggle('slow');
}

function ajaxGetOptions(url, data = null, appendId = null) {
    $.ajax({
        type: "GET",
        url: url,
        data: data,
        dataType: "json",
        success: function (response) {
            if (response.status) {
                let options = `<option value="">Select Account Type</option>`
                response.data.map((item) => {
                    options += `<option value="${item.id}">${item.name}</option>`
                })

                $('#' + appendId)
                    .find('option')
                    .remove()
                    .end()
                    .append(options)

            } else {
                console.log('error')
            }
        },
        error: function () {
            console.log('error!');
        }
    });
}

function fnTransactionApprove(url) {

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to approve this transaction?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: "linear-gradient(310deg, #7928ca, #ff0080)",
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (!result.isConfirmed) {
            return false;
        }

        $.ajax({
            type: "put",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success...',
                        timer: 2000
                    }).then((r) => {
                        window.location.reload();
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg.join('&'),
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.responseJSON.msg.join('&'),
                });
            }
        });
    })
}

function fnTransactionReject(url) {

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to reject this transaction?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: "linear-gradient(310deg, #7928ca, #ff0080)",
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (!result.isConfirmed) {
            return false;
        }

        Swal.fire({
            title: 'What the reason of reject this withdraw?',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                name: 'notes'
            },
            showCancelButton: true,
            confirmButtonText: 'Reject',
            showLoaderOnConfirm: true,
        }).then((result) => {
            if (!result.isConfirmed) {
                return false;
            }

            if (result.value !== '' || result.value !== undefined) {
                url += "?notes=" + result.value
            }

            $.ajax({
                type: "put",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                dataType: "json",
                success: function (response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success...',
                            timer: 2000
                        }).then((r) => {
                            window.location.reload();
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.msg.join('&'),
                        });
                    }
                },
                error: function (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.responseJSON.msg.join('&'),
                    });
                }
            });
        })
    })
}

function fnSuspend(url, rowId) {

    Swal.fire({
        title: 'Are you sure?',
        text: "Once the course is suspended, you will not be able to return it!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: "linear-gradient(310deg, #7928ca, #ff0080)",
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (!result.isConfirmed) {
            return false;
        }

        $.ajax({
            type: "get",
            url: url,
            dataType: "json",
            success: function (response) {

                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success...',
                        timer: 2000
                    });
                    location.reload()
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.msg.join('&'),
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.responseJSON.msg.join('&'),
                });
            }
        });
    })
}
