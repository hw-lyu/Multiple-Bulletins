<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait CommentPaginate
{

    public function commentGetList(string $tableName = 'comment', int $boardIdx = 0, int $offset = 0, int $limit = 100)
    {
        // 코멘트 초깃값;
        $commentData = $this->commentPaginate($tableName, $boardIdx, $offset, $limit);
        $total = $commentData['total'];
        $commentCeil = intval(ceil($total / $limit));

        return [
            'data' => json_decode($commentData['data'], true),
            'total' => $total,
            'ceil' => $commentCeil
        ];
    }

    public function commentPaginate(string $tableName, int $idx, int $offset = 0, int $limit = 10)
    {
        $comment = DB::table($tableName)
            ->where('board_idx', $idx);

        $data = $comment
            ->orderBy('group_idx', 'asc')
            ->orderBy('depth_idx', 'asc')
            ->orderBy('group_order', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get() ?? [];

        $total = $comment->count();

        return [
            'total' => $total,
            'data' => $data
        ];
    }
}
