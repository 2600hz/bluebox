<?php
class Permissions
{
    public function allow($controller, $method, $options = array())
    {
        $userID = users::getAttr('user_id');

        $permission = Doctrine_Query::create()
            ->select('p.permission')
            ->from('Permission p')
            ->where('p.user_id = ?', array($userID))
            ->andWhere('p.controller = ?', array($controller))
            ->andWhere('p.method = ?', array($method))
            ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);

        if (empty($permission)) {
            $permission = Doctrine_Query::create()
                ->select('p.permission')
                ->from('Permission p')
                ->where('p.user_id = ?', array($userID))
                ->andWhere('p.controller = ?', array($controller))
                ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
        }

        switch ($permission) {
            case 'location':
            case 'owner':
                return $permission;
                break;
            case 'custom':
            case 'disabled':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
    }
}