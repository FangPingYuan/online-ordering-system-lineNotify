<?php
session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
$config['debug'] = true;
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['view'] = new \Slim\Views\PhpRenderer('../templates/');

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('pgsql:host=140.127.74.164;port=25432;dbname=yuyteat;', 'yuytlab', '970314970314');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

class loginCheck
{
    private $router;

    public function __construct($router)

    {
        $this->router = $router;
    }

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['isLoggedIn']) and $_SESSION['isLoggedIn'] === 'yes') {
            $response = $next($request, $response);
        } else {
            $response = $response->withRedirect($this->router->pathFor('line'));
        }
        return $response;
    }
}

$app->get('/', function (Request $request, Response $response) {
    $response = $this->view->render($response, "lineAuth.html");
    return $response;
})->setName('line');

$app->get('/login', function (Request $request, Response $response) {
    $response = $this->view->render($response, "logIn.html");
    return $response;
});

$app->post('/checkTel', function (Request $request, Response $response) {
    $id = $_POST['userId'];
    $_SESSION['isLoggedIn'] = 'yes';
    $_SESSION['lineID'] = $id;
    $login = new login($this->db);
    $response = $login->checkCustomerData($id);
    return $response;
});

$app->post('/loginSQL', function (Request $request, Response $response) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $tel = $_POST['tel'];
    $score = $_POST['score'];
    $login = new login($this->db);
    $result = $login->addDataSQL($id, $name, $tel, $score);
    return $result;
});

$app->post('/loginAccToken/{code}', function ($request, $response, $args) {

    $login = new login($this->db);
    $result = $login->getLoginAccToken($args['code']);
    return json_encode($result);
});


$app->get('/notify', function (Request $request, Response $response) {
    $response = $this->view->render($response, "linenotify.html");
    return $response;
});

$app->get('/callbackhomepage', function (Request $request, Response $response) {
    $response = $this->view->render($response, "lineNotifyAccessToken.html");
    return $response;
});

$app->get('/notifyClientID', function ($request, $response, $args) {
    $notify = new notify($this->db);
    $result = $notify->notifyGetClientID();
    return $response->withJson($result);
});


$app->post('/getAccToken', function ($request, $response, $args) {
    $lineID = $_POST['lineID'];
    $notify = new notify($this->db);
    $result = $notify->selAccToken($lineID);
    return json_encode($result);
});

$app->post('/accTokenSQL', function (Request $request, Response $response) {
    $accToken = $_POST["accToken"];
    $getName = $_POST["getName"];

    $notify = new notify($this->db);
    $result = $notify->notifyTokenSQL($accToken, $getName);
});

$app->post('/getNotifyAccToken', function ($request, $response, $args) {
    $getCode = $_POST['code'];
    $notify = new notify($this->db);
    $result = $notify->getAccToken($getCode);
    return json_encode($result);
});

$app->post('/pushMessage', function ($request, $response, $args) {
    $aut = $_POST['aut'];
    $message = $_POST['message'];
    $notify = new notify($this->db);
    $result = $notify->pushMessage($aut, $message);
    return json_encode($result);
});

$app->get('/home', function (Request $request, Response $response) {
    if ($_SESSION['id'] != null) {
        $response = $this->view->render($response, 'home.html');
        return $response;
    }
});

$app->group('', function () use ($app) {

    $app->get('/shop', function (Request $request, Response $response) {
        $response = $this->view->render($response, 'shop.html');
        return $response;
    });

    $app->get('/menuManage', function (Request $request, Response $response) {
        $response = $this->view->render($response, 'menuManage.html');
        return $response;
    });

    $app->get('/order', function (Request $request, Response $response) {
        $home = new order($this->db);
        $result = $home->selectShopid($_SESSION['lineID']);
        if ($result != 0) {
            $response = $this->view->render($response, 'ShopOrder.html');
            return $response;
        } else {
            $response = $this->view->render($response, 'ClientOrder.html');
            return $response;
        }
    });

    $app->get('/shopcreat', function (Request $request, Response $response) {
        $response = $this->view->render($response, 'ShopCreat.html');
        return $response;
    });

    $app->get('/history', function (Request $request, Response $response) {
        $home = new order($this->db);
        $customer = new customer($this->db);
        $result = $customer->checkCustomer($_SESSION['lineID']);
        if ($result == 0) $pass = true;
        else $pass = false;
        $result = $home->selectShopid($_SESSION['lineID']);
        if ($result != 0) {
            $response = $this->view->render($response, 'ShopHistory.html', ['pass' => $pass]);
            return $response;
        } else {
            $response = $this->view->render($response, 'ClientHistory.html', ['pass' => $pass]);
            return $response;
        }
    });
})->add(new loginCheck($container->router));

$app->get('/getShopInfo', function (Request $request, Response $response) {
    $home = new home($this->db);
    $result = $home->getShopInfo();
    return json_encode($result);
});

$app->get('/getShopMenu', function (Request $request, Response $response) {
    $id = _get('id');
    $home = new menu($this->db);
    $result = $home->getShopMenu($id);
    return json_encode($result);
});

$app->get('/getOwnShopMenu', function (Request $request, Response $response) {
    $home = new menu($this->db);
    $result = $home->getOwnShopMenu($_SESSION['lineID']);
    return json_encode($result);
});

$app->get('/postShopMenu', function (Request $request, Response $response) {
    $id = _get('id');
    $name = _get('name');
    $customize = _get('customize');
    $price = _get('price');
    $home = new menu($this->db);
    $result = $home->postShopMenu($id, $name, $customize, $price);
    return json_encode($result);
});

$app->get('/postOwnShopMenu', function (Request $request, Response $response) {
    $name = _get('name');
    $customize = _get('customize');
    $price = _get('price');
    $home = new menu($this->db);
    $result = $home->postOwnShopMenu($_SESSION['lineID'], $name, $customize, $price);
    return json_encode($result);
});

$app->get('/ShopMenu', function (Request $request, Response $response) {
    $shop_name = _get('shop_name');
    $arr = _get('arr');
    $home = new order($this->db);
    $result = $home->postShopMenu($_SESSION['lineID'], $shop_name, $arr);
});

$app->delete('/ShopMenu/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $home = new menu($this->db);
    $result = $home->deleteShopMenu($id);
});

$app->get('/getShopOrder', function (Request $request, Response $response) {
    $home = new order($this->db);
    $result = $home->getShopOrder($_SESSION['lineID']);
    return json_encode($result);
});

$app->get('/getClientOrder', function (Request $request, Response $response) {
    $home = new order($this->db);
    $result = $home->getClientOrder($_SESSION['lineID']);
    return json_encode($result);
});

$app->post('/notifyOrder', function (Request $request, Response $response) {
    $action = $_POST['action'];
    $shop_name = $_POST['shop_name'];
    $num_card = $_POST['num_card'];
    $home = new order($this->db);
    $home->notifyOrder($action, $shop_name, $num_card);
    return json_encode(1);
});

$app->get('/getShopMenuCustomize', function (Request $request, Response $response) {
    $s_id = _get('s_id');
    $m_id = _get('m_id');
    $home = new menu($this->db);
    $result = $home->getShopMenuCustomize($s_id, $m_id);
    return json_encode($result);
});

$app->get('/logout', function ($request, $response) {
    $_SESSION['isLoggedIn'] = 'no';
    return $this->view->render($response, 'home.html');
});

$app->post('/postShop', function ($request, $response) {
    $shop_name = $_POST["shop_name"];
    $shop_phone = $_POST["shop_phone"];
    $seller_name = $_POST["seller_name"];
    $shop_password = $_POST["shop_password"];
    $shop_intro = $_POST["shop_intro"];
    $shop_image = $_POST["shop_image"];
    $creat = new creat($this->db);
    $creat->postShop($shop_name, $shop_phone, $seller_name, $_SESSION['lineID'], $shop_password, $shop_intro, $shop_image);
});

$app->get('/checkID', function (Request $request, Response $response) {
    $home = new customer($this->db);
    $result = $home->checkcustomer($_SESSION['lineID']);
    return json_encode($result);
});

function _get($str)
{
    $val = !empty($_GET[$str]) ? $_GET[$str] : null;
    return $val;
}

function _post($str)
{
    $val = !empty($_POST[$str]) ? $_POST[$str] : null;
    return $val;
}

$app->run();
