<?php
require_once PROJECT_ROOT_PATH . "utils\\SessionInterface.php";
class SessionManager implements SessionInterface
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', 3600);
            session_set_cookie_params(3600);
            //TODO toto setneme priamo do session a v metodach sa budeme obracat priamo na session array keys
            $this->set("time_of_start", new DateTime());
            session_start();
        } else {
            $this->regenerateSessionId();
        }
    }

    public function get(string $key)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return null;
    }

    public function set(string $key, $value): SessionInterface
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function clear(): void
    {
        session_unset();
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function regenerateSessionId(): void
    {
        if ((new DateTime())->diff($this->get("time_of_start")) > new DateInterval("10 minutes")) {
            $this->set("time_of_start", new DateTime());
            session_regenerate_id();
        } else if ((new DateTime())->diff($this->get("time_of_start")) > new DateInterval("30 minutes")) {
           $this->kill();
        }
    }

    public function kill(): void
    {
        session_unset();
        session_destroy();
        //TODO return success kill message;
    }
}
