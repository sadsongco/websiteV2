<?php

require("fpdf.php");
define('GBP',chr(163));

class ORDER_PDF extends FPDF {
    const LILAC = [100, 100, 100];
    const GREY = [220, 220, 220];
    const BLACK = [0, 0, 0];
    const ITEM_GREY = [80, 80, 80];
    const DATE_POS = [120, 42];
    const ADDRESS_POS = [120, 51];
    const ORDER_NO_POS = [25, 87];
    const ITEM_POS = [25, 93];
    const PRICE_X = -30;
    private $pw;

    function Init ($order) {
        $this->SetTitle("The Exact Opposite order ".$order["sumup_id"]);
        $this->SetSubject("The Exact Opposite order ".$order["sumup_id"]);
        $this->SetAuthor("Nigel Powell");
        $this->AddFont('opensansbold','', 'OpenSans-Bold.php');
        $this->AddFont('opensansregular', '', 'OpenSans-Regular.php');
        $this->pw = $this->GetPageWidth();
    }

    function Header () {
        $this->Image('../../../assets/graphics/threelineLogo-email.jpg', 60, 10, 80);
    }

    function Footer () {
        $this->SetFont('opensansregular', '', 9);
        $h = 15;
        $this->SetY(-$h);
        $this->setFillColor(...self::GREY);
        $address = "Dental Records, 52 Claremont Road, Rugby, CV21 3LX, UK";
        $email = "info@theexactopposite.uk";
        $this->SetTextColor(...self::BLACK);
        $this->Cell(0, $h, $address." :: ".$email, 'T', 0, 'C', true, 'mailto:info@theexactopposite.uk');
    }

    function DateCell($order) {
        $this->SetFont('opensansbold', '', 12);
        $this->SetTextColor(...self::BLACK);
        $this->SetDrawColor(...self::LILAC);
        $this->SetXY(...self::DATE_POS);
        $this->Cell(0, 8, $order["order_date"], 'B', 1);
    }

    function AddressCell($order) {
        $this->SetFont('opensansregular', '', 12);
        $address = $order["address_1"];
        if ($order["address_2"] != "") $address.="\n".$order["address_2"];
        $this->SetXY(...self::ADDRESS_POS);
        $this->MultiCell(0, 6,iconv('UTF-8', "CP1250//TRANSLIT", $order["name"])."\n".iconv('UTF-8', "CP1250//TRANSLIT", $address)."\n".iconv('UTF-8', "CP1250//TRANSLIT", $order["city"])."\n".iconv('UTF-8', "CP1250//TRANSLIT", $order["postcode"])."\n".iconv('UTF-8', "CP1250//TRANSLIT", $order["country"]), 'B');
    }

    function OrderNoCell($order_no) {
        $this->SetFont('opensansbold', '', 12);
        $this->SetTextColor(...self::BLACK);
        $this->SetXY(...self::ORDER_NO_POS);
        $this->Cell(0, 8, "Order # ".$order_no, 0, 1);
    }

    function ItemCell ($item) {
        $this->setFont('opensansregular', '', 11);
        $this->SetTextColor(...self::ITEM_GREY);
        $this->SetX(self::ITEM_POS[0]);
        $this->Cell(0, 8, iconv('UTF-8', "CP1250//TRANSLIT", $item["name"]), 0, 0, 'L');
        $this->SetX(self::PRICE_X);
        $this->Cell(0, 8, GBP.$item["price"], 0, 1, 'R');
    }
    
    function ShippingCell($shipping_cost, $shipping_method) {
        $this->SetX(self::ITEM_POS[0]);
        $this->Cell(0, 0, "Postage and packing, $shipping_method", 0, 0, 'L');
        $this->SetX(self::PRICE_X);
        $money_format = new NumberFormatter("en_GB", NumberFormatter::DECIMAL);
        $money_format->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $this->Cell(0, 0, GBP.$money_format->format($shipping_cost), 0, 1, 'R');
    }

    function TotalCell($total) {
        $this->SetX(self::ORDER_NO_POS[0]);
        $this->setFont('opensansbold', '', 16);
        $this->setTextColor(...self::BLACK);
        $this->SetDrawColor(...self::LILAC);
        $this->Cell(0, 8, "TOTAL", 'T', 0, 'L');
        $this->SetX(self::PRICE_X);
        $money_format = new NumberFormatter("en_GB", NumberFormatter::DECIMAL);
        $money_format->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $this->Cell(0, 10, GBP.$money_format->format($total), 'T', 1, 'R');

    }

    function Note($text = "Thank you for buying from The Exact Opposite.\nIt means a lot to us.") {
        $this->SetX(self::ORDER_NO_POS[0]);
        $this->setFont('opensansregular', '', 9);
        $this->MultiCell(0, 7, iconv('UTF-8', "CP1250//TRANSLIT", $text), 0, 1);
    }

    function Spacer($height = 10) {
     $this->Cell(0, $height, '', 0, 1);   
    }

    function OrderDetailsCell ($order) {
        $this->DateCell($order);
        $this->AddressCell($order);
        $this->OrderNoCell($order["sumup_id"]);
        $total = 0;
        $this->SetXY(...self::ITEM_POS);
        foreach ($order["items"] as $item) {
            $this->ItemCell($item);
            $total += $item["price"];
        }
        $this->Spacer();
        $shipping_method = $order['shipping_method'];
        $shipping_cost = $order['shipping'];
        $total += $shipping_cost;
        $this->ShippingCell($shipping_cost, $shipping_method);
        $this->Spacer();
        $this->TotalCell($total);
        $this->Spacer(20);
        $this->Note();
    }
}

function makeOrderPDF($order) {
    $pdf = new ORDER_PDF();
    $pdf->Init($order);
    $pdf->AddPage();
    $pdf->OrderDetailsCell($order);
    $pdf->Output('D', "TEO_order_".$order["sumup_id"].".pdf");
}

?>