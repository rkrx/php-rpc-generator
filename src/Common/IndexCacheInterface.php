<?php

namespace RpcGenerator\Common;

interface IndexCacheInterface {
	public function fetch(): array;
	public function store(array $data): void;
}