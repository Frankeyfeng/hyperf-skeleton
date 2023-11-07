<?php

namespace App\Controller;

/**
 * generateTree generate tree struct
 * @todo move to help
 */
function generateTree($arr, $parent)
{

    $tree = [];
    foreach ($arr as $v) {
        if ($v["parent_id"] == $parent) {
            $children = generateTree($arr, $v["id"]);
            if ($children) {
                $v['children'] = $children;
            }
            $tree[] = $v;
        }
    }

    return $tree;
}

class CaculatorController
{
    private $rawJson = '[{"id":200002538,"name":"空心菜类","level":3,"namePath":"蔬菜/豆制品,叶菜类,空心菜类"},{"id":200002537,"name":"香菜类","level":3,"namePath":"蔬菜/豆制品,葱姜蒜椒/调味菜,香菜类"},{"id":200002536,"name":"紫苏/苏子叶","level":3,"namePath":"蔬菜/豆制品,叶菜类,紫苏/苏子叶"},{"id":200002543,"name":"乌塌菜/塌菜/乌菜","level":3,"namePath":"蔬菜/豆制品,叶菜类,乌塌菜/塌菜/乌菜"},{"id":200002542,"name":"菜心/菜苔类","level":3,"namePath":"蔬菜/豆制品,叶菜类,菜心/菜苔类"},{"id":200002540,"name":"马兰头/马兰/红梗菜","level":3,"namePath":"蔬菜/豆制品,叶菜类,马兰头/马兰/红梗菜"},{"id":200002531,"name":"苋菜类","level":3,"namePath":"蔬菜/豆制品,叶菜类,苋菜类"},{"id":200002528,"name":"其他叶菜类","level":3,"namePath":"蔬菜/豆制品,叶菜类,其他叶菜类"}]';
    public function mul($x, $y)
    {
        return $x * $y;
    }

    public function incr($x)
    {
        return ++$x;
    }

    public function div($x, $y)
    {
        if ($y == 0) {
            throw new \Exception("0 as disivor is not allowed");
        }
        return number_format($x / $y, 2);
    }

    /**
     * conv_tree
     * @description 
     * step1: generate all item to one-dimensional array
     * step2: generate tree
     */
    public function conv_tree()
    {
        $arr = json_decode($this->rawJson, true);

        $map = [];
        $list = [];
        foreach ($arr as $v) {
            $namePathArr = explode(',', $v['namePath']);
            $idPath = [];
            for ($i = 0; $i < count($namePathArr); $i++) {
                $id = uniqid();
                $isLeaf = 2;
                if ($i == count($namePathArr) - 1) {
                    $id = $v['id'];
                    $isLeaf = 1;
                }
                $idPath[] = $id;
                if (!isset($map[$namePathArr[$i]])) {
                    $map[$namePathArr[$i]] = $id;
                    $item = [
                        'id' => $id,
                        'name' => $namePathArr[$i],
                        'name_path' => join(',', array_slice($namePathArr, 0, $i + 1)),
                        'parent_id' => 0,
                        'level' => $i + 1,
                        'is_leaf' => $isLeaf,
                        'id_path' => ',' . join(',', $idPath) . ',',
                    ];

                    if ($i > 0) {
                        $item['parent_id'] = $map[$namePathArr[$i - 1]];
                    }

                    $list[] = $item;
                }
            }
        }

        return generateTree($list, 0);
    }

    public function __call($method, $args)
    {
        throw new \Exception("unknow command.[".$method."] args:". json_encode($args));
    }
}
