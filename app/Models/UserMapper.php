<?php
/**
 * User Mapper
 */
namespace Piton\Models;

class UserMapper extends DataMapperAbstract
{
    protected $table = 'user';
    protected $modifiableColumns = ['email'];
}
