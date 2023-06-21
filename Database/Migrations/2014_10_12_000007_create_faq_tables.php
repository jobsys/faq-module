<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        /**
         * FAQ分组
         */
        Schema::create('faq_groups', function (Blueprint $table) {
            $table->id();
            $table->string('display_name')->comment('分组名称');
            $table->string('name')->index()->comment('分组标识');
            $table->timestamps();
        });

        /**
         * FAQ
         */
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->integer('faq_group_id')->comment('所属分组');
            $table->integer('creator_id')->index()->comment('创建者');
            $table->string('title')->comment('标题');
            $table->text('content')->comment('内容');
            $table->integer('sort_order')->default(0)->comment('排序:数字越大越靠前');
            $table->boolean('is_active')->default(true)->comment('是否激活');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('faq_groups');
        Schema::dropIfExists('faqs');
    }
};
