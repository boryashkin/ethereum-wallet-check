<?php
namespace app\dataTypes;

class Wei
{
    /** @var string wei value like "0x4766ed16e968fb4cc" */
    private $amount;

    public function __construct(string $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string "82.320964911654483148"
     */
    public function getEth()
    {
        return \bcdiv($this->getDecStr(), "1000000000000000000", 18);
    }

    /**
     * @return string "82320964911654483148"
     */
    private function getDecStr()
    {
        return \Phlib\base_convert($this->amount, 16, 10);
    }
}
