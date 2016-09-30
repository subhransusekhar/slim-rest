<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

class AuthenticationMiddleware
{
    public function __invoke($request, $response,  $next)
    {
      if ($request->hasHeader('Authorization')) {
          $request_token = str_replace("OAuth2 ", "", $request->getHeaderLine('Authorization'));
          $code = $this->_verify_auth_token($request_token);
          if($code == 200) {
              return $next($request, $response);
          }
          return $this->error(403);
      }
      else {
        return $this->error(403);
      }

    }
    private function error($code = 500) {
      switch ($code) {
        case '403':
          http_response_code($code);
          $result = array("status" => "403", "message" => "Authorization Error!");
          header("Content-Type: application/json");
          echo json_encode($result);
          exit;
          break;
        case '500':
        default:
          http_response_code($code);
          $result = array("status" => "403", "message" => "Authorization Error!");
          header("Content-Type: application/json");
          echo json_encode($result);
          exit;
          break;
      }

    }
    private function _verify_auth_token($token) {
      $url = 'https://auth.mygov.in/oauth2/tokens/' . $token;
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
      curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
      curl_setopt($ch, CURLOPT_TIMEOUT,10);
      $output = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      return $httpcode;
    }
}
