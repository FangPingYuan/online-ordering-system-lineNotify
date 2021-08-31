<?php
class login
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getLoginAccToken($code)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $ch = curl_init();
        $data = array(
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => "http://140.127.74.168/login",
            "client_id" => "1654087960",
            "client_secret" => "7567a51b1a2f2f48a8193866f5a7b406"
        );
        $data = http_build_query($data);
        curl_setopt($ch, CURLOPT_URL, "https://api.line.me/oauth2/v2.1/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	$response = curl_exec($ch);
	curl_close($ch);
        header("Content-Type: application/json; charset=UTF-8");
        return $response;
    }
    public function checkCustomerData($id){
	
        $sql = "SELECT tel FROM public.customer WHERE code_id = :code_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':code_id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->rowCount();
	return json_encode($result);
    }

    public function addDataSQL($id, $name, $tel, $score)
    {
        header('Content-Type:application/json; charset=UTF-8');
        $sql = "INSERT INTO public.customer (code_id, name, tel, c_score) VALUES (:code_id, :name, :tel, :c_score)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':code_id', $id, PDO::PARAM_STR);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':c_score', $score, PDO::PARAM_INT);
        $stmt->execute();
    }



}
?>
