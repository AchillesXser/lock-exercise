<?php

namespace Src\Util;

class Loop
{
    /**
     * @bool 是否自旋
     */
    public $loop = false;

    /**
     * @int 超时时间
     */
    protected $timeout;

    /**
     * 0.01s
     */
    const MINSECOND = 1e4;

    /**
     * 0.5s
     */
    const MAXSECOND = 5e5;

    public function __construct(int $timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * 执行自旋
     * @param callable $code
     * @return |null
     * @throws \Exception
     */
    public function execute(callable $code)
    {
        $this->loop = true;

        $deadline = microtime(true) + $this->timeout;

        $result = null;
        for ($i = 0; $this->loop && microtime(true) < $deadline; $i++) {
            $result = $code($i);
            // code执行成功退出自旋
            if ($this->loop === false) {
                break;
            }

            // 剩余自旋时间
            $remainTime = ($deadline - microtime(true)) * 1e6;
            if ($remainTime < 0) {
                throw new \Exception("出现异常: 自旋执行超时!");
            }

            // 最小睡眠时间
            $min = min(
                (int)self::MINSECOND * 1.25 ** $i,
                self::MAXSECOND
            );
            $max = min($min * 2, self::MAXSECOND);
            $uTime = min($remainTime, random_int((int)$min, (int)$max));


            usleep($uTime);
        }

        return $result;
    }
}




