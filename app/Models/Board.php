<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class Board extends Model
{
    /**
     * SerializeDate
     */
    use SerializeDate;

    /**
     * 모델과 연결된 테이블 정의
     *
     * @var string
     */
    protected $table = "board_basic";

    /**
     * 모델의 기본 키 정의
     *
     * @var string
     */
    protected $primaryKey = "idx";

    /**
     * 대량 할당(Mass Assignment)할 모델의 속성을 정의
     *
     * @var []
     */
    protected $fillable = [
        "user_email",
        "board_cate",
        "board_title",
        "views",
        "view_like",
        "board_content",
        "photo_state",
        "all_comment",
    ];

    /**
     * 작성 시간 열의 이름 정의
     *
     * @var string
     */
    const CREATED_AT = "view_created_at";

    /**
     * 업데이트 시간 열의 이름 정의
     *
     * @var string
     */
    const UPDATED_AT = "view_updated_at";
}
