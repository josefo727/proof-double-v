<?php

namespace Tests\Unit\Services;

use App\Services\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    /** @test */
    public function should_validate_status_names(): void
    {
        $this->assertTrue(OrderStatus::isValid(OrderStatus::CREATED));
        $this->assertTrue(OrderStatus::isValid(OrderStatus::ACCEPTED));
        $this->assertTrue(OrderStatus::isValid(OrderStatus::DISTRIBUTION));
        $this->assertTrue(OrderStatus::isValid(OrderStatus::DELIVERED));
        $this->assertTrue(OrderStatus::isValid(OrderStatus::CANCELLED));

        $this->assertFalse(OrderStatus::isValid('invalid_status'));
    }

    /** @test */
    public function should_return_label_for_status(): void
    {
        $status = new OrderStatus(OrderStatus::CREATED);
        $this->assertEquals('Creada', $status->getLabel());
    }

    /** @test */
    public function should_allow_valid_status_transitions(): void
    {
        $status = new OrderStatus(OrderStatus::CREATED);
        $this->assertTrue($status->canChangeStatus(OrderStatus::ACCEPTED));
        $this->assertTrue($status->canChangeStatus(OrderStatus::CANCELLED));
        $this->assertFalse($status->canChangeStatus(OrderStatus::DELIVERED));
    }

    /** @test */
    public function should_throw_exception_for_invalid_status(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OrderStatus('invalid_status');
    }
}
