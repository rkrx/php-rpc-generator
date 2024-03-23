<?php

namespace RpcGenerator\Entities;

use RpcGenerator\Attributes\RPCClass;
use RpcGenerator\Attributes\RPCMethod;

#[RPCClass(module: 'customers', name: null, requiredRight: 'customers')]
class CustomerService {
	#[RPCMethod(name: 'getCustomers')]
	public function getAll(): array {
		return ['test'];
	}

	#[RPCMethod()]
	public function getCustomer(int|string $id): string {
		return 'test';
	}
}
