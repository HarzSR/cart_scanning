<?php

class cart
{
    // Function to return List of Products

    public function getProducts()
    {
        $products = [
            ["product" => "A", "price" => 1.25, "bulk" => 3, "bulk_price" => 3.00],
            ["product" => "B", "price" => 4.25, "bulk" => 0, "bulk_price" => 0.00],
            ["product" => "C", "price" => 1.00, "bulk" => 6, "bulk_price" => 5.00],
            ["product" => "D", "price" => 0.75, "bulk" => 0, "bulk_price" => 0.00],
        ];
        return $products;
    }

    // To Add to Product

    public function addToCart()
    {
        $id = intval($_GET["id"]);
        if ($id > 0) {
            if ($_SESSION['cart'] != "") {
                $cart = json_decode($_SESSION['cart'], true);
                $found = false;
                for ($i = 0; $i < count($cart); $i++)
                {
                    if ($cart[$i]["product"] == $id)
                    {
                        $cart[$i]["quantity"] = $cart[$i]["quantity"] + 1;
                        $found = true;
                        break;
                    }
                }
                if (!$found)
                {
                    $line = new stdClass;
                    $line->product = $id;
                    $line->quantity = 1;
                    $cart[] = $line;
                }
                $_SESSION['cart'] = json_encode($cart);
            }
            else
            {
                $line = new stdClass;
                $line->product = $id;
                $line->quantity = 1;
                $cart[] = $line;
                $_SESSION['cart'] = json_encode($cart);
            }
        }
    }

    // Remove from Cart

    public function removeFromCart()
    {
        $id = intval($_GET["id"]);
        if ($id > 0)
        {
            if ($_SESSION['cart'] != "")
            {
                $cart = json_decode($_SESSION['cart'], true);
                for ($i = 0; $i < count($cart); $i++)
                {
                    if ($cart[$i]["product"] == $id)
                    {
                        $cart[$i]["quantity"] = $cart[$i]["quantity"] - 1;
                        if ($cart[$i]["quantity"] < 1)
                        {
                            unset($cart[$i]);
                        }
                        break;
                    }
                }
                $cart = array_values($cart);
                $_SESSION['cart'] = json_encode($cart);
            }
        }
    }

    // Delete Cart

    public function emptyCart()
    {
        $_SESSION['cart'] = "";
    }

    // Retrieve Cart

    public function getCart()
    {
        $cartArray = array();
        if (!empty($_SESSION['cart']))
        {
            $cart = json_decode($_SESSION['cart'], true);
            for ($i = 0; $i < count($cart); $i++)
            {
                $lines = $this->getProductData($cart[$i]["product"]);
                $line = new stdClass;
                $line->id = $cart[$i]["product"];
                $line->price = $lines['price'];
                $line->quantity = $cart[$i]["quantity"];
                if($lines["bulk"] != 0)
                {
                    if($cart[$i]["quantity"] > $lines["bulk"])
                    {
                        $line->total = round($lines['bulk_price'] * (int)($cart[$i]["quantity"] / $lines["bulk"]), 2) + round($lines['price'] * ($cart[$i]["quantity"]%$lines["bulk"]), 2);
                    }
                    else if($cart[$i]["quantity"] == $lines["bulk"])
                    {
                        $line->total = round($lines['bulk_price'] * (int)($cart[$i]["quantity"] / $lines["bulk"]), 2);
                    }
                    else
                    {
                        $line->total = round($lines['price'] * $cart[$i]["quantity"], 2);
                    }
                }
                else
                {
                    $line->total = round($lines['price'] * $cart[$i]["quantity"], 2);
                }
                $line->product = $lines['product'];

                $cartArray[] = $line;
            }
        }
        return $cartArray;
    }

    // Product Data

    private function getProductData($id)
    {
        $products = $this->getProducts();
        return $products[($id - 1)];
    }
}
