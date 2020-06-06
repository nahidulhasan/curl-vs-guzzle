<html lang="en">
<head>
    <title>BL Recharge</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>

@php
    $base_url = env('SSL_API_HOST');
    $number = preg_replace("/[^0-9]/", "", $_GET['topup_number']);
@endphp

<div class="container">
    <h2></h2>
    <h2></h2>
    <form action="{{$base_url}}/initiate-recharge" method="post">

        <input type="hidden" name="_token" id="csrf-token" value="uoTk0bcYDN8wbIPsgXR3O7MflTrdUZioMsSj9fz7" />

        <div class="form-group">
            <label for="msisdn">Mobile No:</label>
            <input type="number" class="form-control" id="msisdn"   value="{{$number}}" name="msisdn[]" readonly>
        </div>

        <div class="form-group">
            <label for="connection_type">Connection Type:</label>
            <input type="text"  class="form-control" id="connection_type" value="{{$_GET['connection_type']}}"  name="connection_type[]" readonly>
        </div>

        <div class="form-group">
            <label for="amount">Amount :</label>
            <input type="number" class="form-control" id="amount" placeholder="Enter amount" value="{{$_GET['amount']}}" name="amount[]" readonly>
        </div>
        <div class="form-group">
            <label for="trns_id">Transaction ID :</label>
            <input type="text" class="form-control" id="trns_id" placeholder="Enter transaction" value="{{$_GET['tran_id']}}" name="trns_id" readonly>
        </div>
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" class="form-control" id="email" placeholder="Enter email" value="{{$_GET['email']}}" name="email" readonly>
        </div>
        <div class="form-group">
            <label for="card_name">Card Name :</label>
            <input type="text" class="form-control" id="card_name" placeholder="Enter card name" name="card_name">
        </div>
        <div class="form-group">
            <label for="cus_name">Customer Name :</label>
            <input type="text" class="form-control" id="cus_name" placeholder="Enter customer name" name="cus_name">
        </div>
        <div class="form-group">
            <label for="total_amount">Total Amount :</label>
            <input type="number" class="form-control" id="total_amount" placeholder="Enter total amount" value="{{$_GET['amount']}}" name="total_amount" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

</body>
</html>
