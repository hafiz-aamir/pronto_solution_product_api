<html>

<body style="background-color:#ffffff; font-family: Open Sans, sans-serif;font-size:100%;font-weight:400;line-height:1.4;color:#000;">
  <table style="max-width:670px;margin:50px auto 10px;background-color:#fff;padding:50px;border-radius:3px;border: 1px solid #000;border-top:solid 10px #000;">
    <thead>
      <tr>
        <th style="text-align:left; padding:50px 15px 0 15px;">
            <img src="{{$details['Logo']}}" alt="{{$details['WebsiteName']}}" />
            <!--{{$details['WebsiteName']}}-->
        </th>
        <th style="text-align:right; padding:50px 15px 0 15px;">{{$details['currentDate']}}</th>
      </tr>
    </thead>
    
    
    <tbody>
     
        <tr>
        
        <td colspan="2" style="border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; border-bottom:1px solid #000; padding:10px 20px;">
          
         
            <span style="font-weight:bold; display:inline-block;min-width:146px"> ORDER CONFIRMATION </span>
            <hr>
                
            <br>
          
              <span style="display:inline-block;min-width:146px">HI {{$details['first_name']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Country : {{$details['country']}} </span> <br> <hr>
              
              <span style="display:inline-block;min-width:146px">City : {{$details['city']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">State : {{$details['state']}} </span> <br>
              <hr>
              
              <span style="display:inline-block;min-width:146px">Address : {{$details['address']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Zipcode : {{$details['zipcode']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Phoneno : {{$details['phoneno']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Total : £{{$details['total']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Discount : £{{$details['discount']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Order Email : {{$details['order_email']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Order Status : {{$details['order_status']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Transaction ID : {{$details['transaction_id']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Customer ID : {{$details['customer_id']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Card Payment : {{$details['card_payment']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Payment Method : {{$details['payment_method']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px">Tracking No : {{$details['tracking_no']}} </span> <br><hr>
              
              <span style="display:inline-block;min-width:146px"> <a href="{{$details['receipt_url']}}"> Receipt Link </a> </span> <br>
              
              <hr>
              
              <span style="font-weight:bold; display:inline-block;min-width:146px"> KINDLY CHECK MORE INFORMATION IN YOUR ORDER DETAILS </span>
             
        </td>
        
      </tr>
      
      

    </tbody>
    <tfooter>
      <tr>
        <td colspan="2" style="font-size:14px;padding:50px 15px 0 15px;">
          <strong style="display:block;margin:0 0 10px 0;">Regards</strong>{{$details['WebsiteName']}}<br> <br>
          <b>Phone:</b> {{$details['companyPhone']}}<br>
          <b>Email:</b>{{$details['companyEmail']}}
        </td>
      </tr>
    </tfooter>
  </table>
</body>

</html>