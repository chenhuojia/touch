<?php
/**
 +------------------------------------------------------------------------------
 * Tree 构建tree状数据
 +------------------------------------------------------------------------------
 * @version   v1.4
 +------------------------------------------------------------------------------
 */
 namespace chj\tree;
class Tree
{
    /**
     * 主键名称
     * @var string
     */
    private static $primary = 'id';
    /**
     * 父键名称
     * @var string
     */
    private static $parentId = 'parent_id';
    /**
     * 子节点名称
     * @var string
     */
    private static $child    = 'child';
    /**
     * 修改主键名称、父键名称、子节点名称
     * @param string $primary
     * @param string $parentId
     * @param string $child
     */
    public static function setConfig($primary = '', $parentId = '', $child = ''){
        if(!empty($primary))  self::$primary  = $primary;
        if(!empty($parentId)) self::$parentId = $parentId;
        if(!empty($child))    self::$child    = $child;
    }
    /**
     * 生成Tree
     * @param array $data
     * @param number $index
     * @return array
     */
    public static  function  makeTree(&$data, $index = 0)
    {
        $childs = self::findChild($data, $index);
        if(empty($childs))
        {
            return $childs;
        }
        foreach($childs as $k => &$v)
        {
            if(empty($data)) break;
            $child = self::makeTree($data, $v[self::$primary]);
            if(!empty($child))
            {
                $v[self::$child] = $child;
            }else{
                $v[self::$child] = [];
            }
        }
        unset($v);
        return $childs;
    }
    /**
     * 查找子类
     * @param array $data
     * @param number $index
     * @param bool $all
     * @return array
     */
    public static function findChild(&$data, $index,$all = false)
    {
        $childs = [];
		foreach ($data as $k => $v){
			if($v[self::$parentId] == $index){
				if ($all)
                {
                    $tmp = $v;
                    $tmp['child'] = self::findChild($data,$v[self::$primary],$all);
                    $childs[] = $tmp;
                }else{
                    $childs[]  = $v;
                }
			}
		}
		return $childs;
    }

    /**
     * 查找父类
     * @param $data
     * @param $index
     * @param bool $all
     * @return array|mixed
     */
    public static function findParent($data,$index,$all=false)
    {
        $parents = [];
        foreach ($data as $k=>$v)
        {
            if ($v[self::$primary] == $index)
            {
                $tmp = $v;
                if ($all)
                {
                    if ($v[self::$primary] != 0)
                    {
                        $tmp['parent'] = self::findParent($data,$v[self::$parentId],$all);
                        $parents = $tmp;
                    }
                }else{
                    if ($v[self::$primary] != 0)
                    {
                        foreach ($data as $key=>$value)
                        {
                            if ($value[self::$primary] == $v[self::$parentId])
                            {
                                $tmp['parent'] = $value;
                                return  $tmp;
                            }
                        }
                    }
                }
            }
        }
        return $parents;
    }
}
