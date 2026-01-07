<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\PostTooLargeException;

class CustomValidatePostSize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Http\Exceptions\PostTooLargeException
     */
    public function handle($request, Closure $next)
    {
        // Luôn cho phép upload lên đến 150MB, bất kể giá trị trong php.ini
        $max = $this->getPostMaxSize();
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);

        // Chỉ kiểm tra nếu CONTENT_LENGTH lớn hơn 150MB
        if ($contentLength > 0 && $contentLength > $max) {
            throw new PostTooLargeException('The POST data is too large. Maximum allowed size is 150MB.');
        }

        return $next($request);
    }

    /**
     * Determine the server 'post_max_size' as bytes.
     * Override để cho phép upload lớn hơn
     *
     * @return int
     */
    protected function getPostMaxSize()
    {
        // Luôn trả về 150MB để bypass kiểm tra của Laravel
        // Lưu ý: Vẫn cần sửa php.ini để PHP thực sự chấp nhận upload lớn
        return 150 * 1024 * 1024; // 150MB = 157286400 bytes
    }
}

