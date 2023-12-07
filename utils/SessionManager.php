<?php
require_once PROJECT_ROOT_PATH . "utils\\SessionInterface.php";
class SessionManager implements SessionInterface
{
    private int $sessionlifetime = 3600;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', $this->sessionlifetime);
            session_set_cookie_params($this->sessionlifetime);
            $this->initializeSession();
        } else {
            $this->regenerateSessionId();
        }
    }

    private function initializeSession(): void
    {
        session_start();
        $this->set('time_of_start', new DateTimeImmutable());
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
        $timeOfStart = $this->get("time_of_start");

        if ((new DateTimeImmutable())->diff($timeOfStart)->i > 1) {
            $this->set("time_of_start", new DateTimeImmutable());
            ini_set('session.gc_maxlifetime',$this->sessionlifetime);
            session_set_cookie_params($this->sessionlifetime);
            session_regenerate_id();
        } elseif ((new DateTimeImmutable())->diff($timeOfStart)->i > 30) {
            
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
