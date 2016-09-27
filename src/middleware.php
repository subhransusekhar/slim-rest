<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticationMiddleware
{
    public function __invoke($request, $response,  $next)
    {
      if ($request->hasHeader('Authorization')) {
          if($request->getHeaderLine('Authorization') == 'Get me in') {
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
}
