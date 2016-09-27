<?php
/**
 * 文件上传处理文件
 * User: zhangh
 * Date: 16-3-22
 * Time: 上午9:18
 */
include_once('Watermark.php');

//接收上传图片
if(!empty($_FILES)) {
    // 获取参数
    $file = $_FILES['file'];
    $type = isset($_POST['type']) ? intval($_POST['type']) : 1;
    $pos = isset($_POST['pos']) ? intval($_POST['pos']) : 1;
    $size = isset($_POST['size']) ? $_POST['size'] : [];

    // 验证图片的类型及大小
    if (!in_array($file['type'], array('image/jpeg', 'image/png')) {
        echo '上传文件类型错误，请重试！';
        exit;
    }
    if (sprintf('%.2f', $file['size'] / 1024) > 1024) {
        echo '上传文件过大，请重试！';
        exit;
    }

    // 图片上传处理
    $pInfo = pathinfo($file['name']);
    $destination = 'uploads/' . time() . '.' . $pInfo['extension'];
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo '文件上传出错，请重试';
    }

    // 处理图片（水印、拉伸、压缩）
    $wm = new Watermark($type, $pos, $size, $destination);
    $wm->hit($destination);
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>图片上传</title>
</head>
<body>

<h3>图片上传</h3>
<form action="" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td>文件：</td>
            <td><input type="file" name="file"/></td>
        </tr>
        <tr>
            <td>水印类型：</td>
            <td>
                <input type="radio" name="type" value="1" <?php echo isset($type) && $type == 1 ? 'checked' : ''; ?>/>图片
                <input type="radio" name="type" value="2" <?php echo isset($type) && $type == 2 ? 'checked' : ''; ?>/>文字
            </td>
        </tr>
        <tr>
            <td>水印位置：</td>
            <td>
                <input type="radio" name="pos" value="1" <?php echo isset($pos) && $pos == 1 ? 'checked' : ''; ?>/>左上角
                <input type="radio" name="pos" value="2" <?php echo isset($pos) && $pos == 2 ? 'checked' : ''; ?>/>居中
                <input type="radio" name="pos" value="3" <?php echo isset($pos) && $pos == 3 ? 'checked' : ''; ?>/>右下角
            </td>
        </tr>
        <tr>
            <td>图片类型：</td>
            <td>
                <input type="checkbox" name="size[]" value="1" <?php echo isset($size) && in_array(1, $size) == 1 ? 'checked' : ''; ?>/>大图（拉伸5倍）
                <input type="checkbox" name="size[]" value="2" <?php echo isset($size) && in_array(2, $size) == 2 ? 'checked' : ''; ?>/>小图（压缩5倍）
            </td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" value="Upload"></td>
        </tr>
    </table>
</form>

<br/><br/>
<?php
    echo isset($destination) ? "<h3>图片展示：</h3><img src='{$destination}'>" : '';
?>
</body>
</html>