<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $pembayaran = isset($_POST['pembayaran']) ? $_POST['pembayaran'] : '';
    $grandTotal = isset($_POST['grandTotal']) ? $_POST['grandTotal'] : '';

    echo "<br><strong>MASTER</strong><br>";
    echo "ID : " . $id . "<br>Pembayaran : " . $pembayaran . "<br>Grand Total : " . $grandTotal . "<br>";

    $name = isset($_POST['name']) ? $_POST['name'] : array();
    $price = isset($_POST['price']) ? $_POST['price'] : array();
    $qty = isset($_POST['qty']) ? $_POST['qty'] : array();
    $total = isset($_POST['total']) ? $_POST['total'] : array();

    echo "<br><strong>DETAIL</strong><br>";

    foreach ($name as $key => $val) {
        echo "Name: " . $name[$key] . ", Price: " . $price[$key] . ", Qty: " . $qty[$key] . ", Total: " . $total[$key] . "<br>";
    }

    exit();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Simple POS Application</title>

        <!-- StyleSheet -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.6/css/mdb.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">

        <style type="text/css">
            html, body {
                height: 100%;
            }

            #sidebar {
                width: 500px;
                height: 100%;
                position: fixed;
                top: 0;
                right: 0;
                overflow-y: scroll;
                background-color: #fff;
                z-index: 999;
            }

            #sidebar > main {
                margin-bottom: 150px;
            }

            #sidebar > footer {
                width: 500px;
                position: fixed;
                right: 0;
                bottom: 0;
            }

            #sidebar .table td, #sidebar .table th {
                padding: .5rem .8rem;
                vertical-align: middle;
                font-family: tahoma;
            }

            #content {
                height: 100%;
                margin-right: 485px;
                overflow: hidden;
            }

            #content > main {
                height: calc(100% - 60px);
                padding-top: 10px;
                overflow-y: scroll;
            }

            .img-info {
                position: absolute;
                left: 0;
                bottom: 8px;
                width: 100%;
                padding: 0 8px;
            }

            .img-info > span {
                background-color: rgba(0, 0, 0, 0.5);
                color: #fff;
                display: block;
                padding: 10px;
                font-size: 14px;
            }

            .pick-product {
                cursor: pointer;
            }

            .edit-product {
                max-width: 70px;
            }
        </style>

        <!-- JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.6/js/mdb.min.js" defer></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $(".pick-product").click(function () {
                    var name = $(this).find(".name").text();
                    var price = $(this).find(".price").text();
                    var qty = 1;
                    var total = (parseFloat(price) * parseInt(qty));

                    var row = '<tr>' +
                            '<td><strong>' + name + '</strong><br>' + price + '</td>' +
                            '<td><input type="number" name="qty[]" min="1" value="' + qty + '" class="edit-product"></td>' +
                            '<td class="total text-right">' + total + '</td>' +
                            '<td>' +
                            '<input type="hidden" name="name[]" value="' + name + '">' +
                            '<input type="hidden" name="price[]" value="' + price + '">' +
                            '<input type="hidden" name="total[]" value="' + total + '">' +
                            '<a href="javascript:void(0);" class="delete-product"><i class="fas fa-trash"></i></a>' +
                            '</td>' +
                            '</tr>';

                    $("#sidebar table > tbody").append(row);
                    $("#sidebar").animate({scrollTop: $("#sidebar table").height()}, 1000);

                    $(".edit-product").off().change(function () {
                        var item = $(this).parent().parent();
                        var price = $(item).find('input[name="price[]"]').val();
                        var qty = $(item).find('input[name="qty[]"]').val();
                        var total = (parseFloat(price) * parseInt(qty));

                        $(item).find('input[name="total[]"]').val(total);
                        $(item).find('td.total').text(total);

                        setTotal();
                    });

                    $(".delete-product").off().click(function () {
                        $(this).parent().parent().remove();

                        setTotal();
                    });

                    setTotal();
                });

                $("[name='dibayar']").change(function () {
                    var total = $("#paymentModal [name='total']").val();
                    var dibayar = $("#paymentModal [name='dibayar']").val();
                    var kembali = (parseFloat(total) - parseFloat(dibayar));

                    $("#paymentModal [name='kembali']").val(kembali);
                });
            });

            function saveData() {
                var total = $("#paymentModal [name='total']").val();
                var pembayaran = $("#paymentModal [name='pembayaran']").val();

                $("#transaction [name='grandTotal']").val(total);
                $("#transaction [name='pembayaran']").val(pembayaran);
                $("#transaction").submit();
            }

            function setTotal() {
                var tot = 0;

                $("input[name='total[]']").each(function () {
                    tot += parseFloat($(this).val());
                });

                $("h3.total").text(tot);
                $("#paymentModal [name='total']").val(tot);
            }
        </script>
    </head>
    <body class="grey lighten-3">
        <div id="sidebar" class="shadow">
            <main>
                <form id="transaction" method="post">
                    <input type="hidden" name="id" value="<?php echo 'TRAN-' . date('YmdHis'); ?>">
                    <input type="hidden" name="pembayaran" value="">
                    <input type="hidden" name="grandTotal" value="">

                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th scope="col">PRODUCT</th>
                                <th scope="col">QTY</th>
                                <th scope="col" class="text-right">PRICE</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- -->
                        </tbody>
                    </table>
                </form>
            </main>
            <footer class="primary-color text-white">
                <div class="row d-none">
                    <div class="col-12 pr-5 pl-4 py-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Barcode">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-lg btn-indigo btn-block m-2" data-toggle="modal" data-target="#paymentModal">
                            <i class="far fa-money-bill-alt mr-1"></i> PAYMENT
                        </button>
                    </div>
                    <div class="col-5">
                        <div class="text-right mt-2">
                            <strong>TOTAL</strong>
                            <h3 class="total">0</h3>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <div id="content">
            <header>
                <nav class="navbar navbar-dark primary-color">
                    <a class="navbar-brand" href="#">
                        <strong>Easy POS</strong>
                    </a>
                    <form class="mr-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search">
                            <div class="input-group-append">
                                <button class="btn btn-md btn-indigo m-0" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </nav>
            </header>
            <main>
                <div class="container-fluid">
                    <div class="row px-2">
                        <?php
                        for ($i = 0; $i < 100; $i++) {
                            $f = $i % 9;

                            echo '<div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 p-2">
                                <div class="bg-white h-100 text-center pick-product">
                                    <img src="files/' . $f . '.png" class="img-fluid" alt="Product Name ' . $i . '" style="max-height: 200px; width: 100%;">
                                    <div class="img-info">
                                        <span class="name">Product Name ' . $i . '</span>
                                        <span class="price d-none">' . (($i + 15) * 3400) . '</span>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content primary-color-dark text-white">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">PEMBAYARAN</label>
                            <select class="form-control" name="pembayaran">
                                <option>Cash</option>
                                <option>Debit Card</option>
                                <option>Credit Card</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">TOTAL</label>
                            <input type="number" name="total" class="form-control text-right" readonly>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">DIBAYAR</label>
                            <input type="number" name="dibayar" class="form-control text-right">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">KEMBALI</label>
                            <input type="number" name="kembali" class="form-control text-right" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-lg btn-block" onclick="saveData();">SAVE</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>