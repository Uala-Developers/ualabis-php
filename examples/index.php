<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SDK TESTS</title>
  <style>
    table {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    td,
    th {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
    }

    tr:nth-child(even) {
      background-color: #dddddd;
    }
  </style>
</head>

<body>
  <h1>SDK TESTS</h1>
  <?php
  require_once('../vendor/autoload.php');

  use Uala\SDK;

  $sdk = new SDK('your_username', 'your_client_id', 'your_client_secret_id', true);
  
  $order = $sdk->createOrder(11, 'test', 'https://www.google.com/', 'https://www.google.com/');
  $generatedOrder = $sdk->getOrder($order->uuid);
    ?>
  <h2>New Order</h2>
  <table>
    <tr>
      <th>uuid</th>
      <th><?php echo $order->uuid ?></th>
    </tr>
    <tr>
      <td>amount</td>
      <td><?php echo $order->amount ?></td>
    </tr>
    <tr>
      <td>checkout link</td>
      <td><a href="<?php echo $order->links->checkoutLink ?>"><?php echo $order->links->checkoutLink ?></a></td>
    </tr>
  </table>

  <h2>Generated Order</h2>
  <table>
    <tr>
      <th>uuid</th>
      <th><?php echo $generatedOrder->order_id ?></th>
    </tr>
    <tr>
      <td>status</td>
      <td><?php echo $generatedOrder->status ?></td>
    </tr>
    <tr>
      <td>amount</td>
      <td><?php echo $generatedOrder->amount ?></td>
    </tr>
  </table>

</body>

</html>