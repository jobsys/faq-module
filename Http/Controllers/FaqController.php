<?php

namespace Modules\Faq\Http\Controllers;

use App\Http\Controllers\BaseManagerController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Faq\Entities\Faq;
use Modules\Faq\Entities\FaqGroup;
use Modules\Starter\Emnus\State;

class FaqController extends BaseManagerController
{
    /**
     * Display a listing of the resource.
     */
    public function pageFaq()
    {

        $group_options = FaqGroup::get(['id', 'display_name'])->map(function (FaqGroup $item) {
            return [
                'value' => $item->id,
                'label' => $item->display_name,
            ];
        })->toArray();

        return Inertia::render('PageFaq@Faq', [
            'groupOptions' => $group_options,
        ]);
    }


    public function groupItems(Request $request)
    {
        $display_name = $request->input('display_name');

        $pagination = FaqGroup::withCount(['faqs'])
            ->when($display_name, function ($query) use ($display_name) {
                return $query->where('display_name', 'like', '%' . $display_name . '%');
            })
            ->paginate();

        return $this->json($pagination);
    }

    public function groupEdit(Request $request)
    {

        list($input, $error) = land_form_validate(
            $request->only('id', 'display_name'),
            [
                'display_name' => 'bail|required|string',
            ],
            [
                'display_name' => '分类名称',
            ]
        );

        if ($error) {
            return $this->message($error);
        }

        $unique = land_is_model_unique($input, FaqGroup::class, 'display_name', true);

        if (!$unique) {
            return $this->message('该分类已经存在');
        }


        if (isset($input['id']) && $input['id']) {
            $result = FaqGroup::where('id', $input['id'])->update($input);
        } else {
            $name = pinyin_abbr($input['display_name']);

            $index = 1;
            while (!land_is_model_unique(['name' => $name], FaqGroup::class, 'name', true)) {
                $name = $name . $index;
                $index += 1;
            }
            $input['name'] = $name;
            $result = FaqGroup::create($input);
        }


        log_access(isset($input['id']) && $input['id'] ? '编辑常见问题分类' : '新建常见问题分类', $input['id'] ?? $result->id);

        return $this->json(null, $result ? State::SUCCESS : State::FAIL);
    }

    public function groupDelete(Request $request)
    {
        $id = $request->input('id');
        $item = FaqGroup::where('id', $id)->first();

        if (!$item) {
            return $this->json(null, State::NOT_ALLOWED);
        }

        $is_empty = Faq::where('faq_group_id', $id)->count() == 0;

        if (!$is_empty) {
            return $this->json(null, '该分类下还有内容，删除失败');
        }

        $result = $item->delete();

        log_access('删除常见问题分类', $id);
        return $this->json(null, $result ? State::SUCCESS : State::FAIL);
    }

    public function items(Request $request)
    {

        $faq_group_id = $request->input('faq_group_id', false);
        $title = $request->input('title', false);

        $pagination = Faq::with(['group:id,display_name'])->when($faq_group_id, function ($query, $faq_group_id) {
            $query->where('faq_group_id', $faq_group_id);
        })->when($title, function ($query, $title) {
            $query->where('title', 'like', "%{$title}%");
        })->select(['id', 'title', 'faq_group_id', 'sort_order', 'is_active', 'created_at'])
            ->orderByDesc('sort_order')->paginate();

        log_access('查看常见问题列表');

        return $this->json($pagination);
    }

    public function item(Request $request, $id)
    {
        $item = Faq::where('id', $id)->first();

        if (!$item) {
            return $this->json(null, State::NOT_FOUND);
        }

        log_access('查看常见问题详情', $id);

        return $this->json($item);
    }

    public function edit(Request $request)
    {

        list($input, $error) = land_form_validate(
            $request->only('id',  'title', 'content', 'faq_group_id',  'sort_order', 'is_active'),
            [
                'faq_group_id' => 'bail|required|numeric',
                'title' => 'bail|required|string',
                'content' => 'bail|required|string',
            ],
            [
                'faq_group_id' => '分类',
                'title' => '标题',
                'content' => '内容',
            ]
        );

        if ($error) {
            return $this->message($error);
        }



        if (isset($input['id']) && $input['id']) {
            $result = Faq::where('id', $input['id'])->update($input);
        } else {
            $input['creator_id'] = $this->login_user_id;
            $result = Faq::create($input);
        }
        log_access(isset($input['id']) && $input['id'] ? '编辑常见问题' : '新建常见问题', $input['id'] ?? $result->id);

        return $this->json(null, $result ? State::SUCCESS : State::FAIL);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $item = Faq::where('id', $id)->first();

        if (!$item) {
            return $this->json(null, State::NOT_ALLOWED);
        }

        $result = $item->delete();

        log_access('删除常见问题', $id);
        return $this->json(null, $result ? State::SUCCESS : State::FAIL);
    }


}
