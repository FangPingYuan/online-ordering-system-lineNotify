<?php
class notify
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function selAccToken($client_id)
    {
        $stmt = $this->db->prepare("SELECT notify_acctoken
                         FROM public.customer
                         WHERE code_id = :client_id");
        $stmt->bindParam(':client_id', $client_id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $row;
    }

    public function notifyTokenSQL($accToken, $getName)
    {
	header('Content-Type:application/json; charset=UTF-8');
        $stmt = $this->db->prepare("UPDATE public.customer 
			 SET notify_acctoken = :accToken
			 WHERE name = :getName");
        $stmt->bindParam(':accToken', $accToken, PDO::PARAM_STR);
        $stmt->bindParam(':getName', $getName, PDO::PARAM_STR);
        $stmt->execute();
    }

   public function notifyGetClientID()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $client_id = 'PUi5A5z65xy18PGMu18Pa1';

        return $client_id;
    }

    public function pushMessage($aut,$message)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ch = curl_init();
            $data = "message=".$message;
            curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/x-www-form-urlencoded',
                'Authorization:' . $aut
            ));

            $response = curl_exec($ch);
            return $response;
            curl_close($ch);
        }
    }

    public function getAccToken($code)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $ch = curl_init();
        $data = array(
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => "http://140.127.74.168/callbackhomepage",
            "client_id" => "PUi5A5z65xy18PGMu18Pa1",
            "client_secret" => "Q6Vqhm3lxqD2cLSIUuqpjUpK0piRU0VZwfjbUjUI64Q"
        );

        curl_setopt($ch, CURLOPT_URL, "https://notify-bot.line.me/oauth/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, array('content-Type:application/x-www-form-urlencoded'));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
