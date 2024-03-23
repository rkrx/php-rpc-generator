<?php

namespace RpcGenerator\Entities;

use RpcGenerator\Attributes\RPCClass;
use RpcGenerator\Attributes\RPCMethod;

#[RPCClass(module: 'orders', name: 'Orders', requiredRight: 'orders')]
class OrderService {
	#[RPCMethod(name: 'getOrders')]
	public function getAll(): array {
		return ['test'];
	}

	#[RPCMethod(name: 'getOrder')]
	public function get(string $id): string {
		return 'test';
	}
}
