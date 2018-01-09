<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Topics;
use App\Moldes\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\User;

class TopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index(Request $request, Topics $topic, User $user)
    {
        $active_users = $user->getActiveUsers();
        $topics = $topic->withOrder($request->order)->paginate(20);

        return view('topics.index',compact('topics','active_users'));
    }

    public function show(Request $request, Topics $topic)
    {
        // URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }
        return view('topics.show', compact('topic'));
    }

    public function create(Topics $topic)
    {
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    public function store(TopicRequest $request, Topics $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();
        return redirect($topic->link())->with('success', '成功创建主题！');
    }

    public function edit(Topics $topic)
    {
       // dd($topic);
        $this->authorize('update', $topic);
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    public function update(TopicRequest $request, Topics $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return redirect($topic->link())->with('success', '更新成功！');
    }

    public function destroy(Topics $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();
        return redirect()->route('topics.index')->with('success', '成功删除！');
    }

    public function imageUpload(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id().time().str_random(10), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }
}