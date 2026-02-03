import os
import re

def fix_utf8_functions(file_path):
    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    # Pattern to find utf8_encode and utf8_decode
    encode_pattern = r'utf8_encode\s*\((.*?)\)'
    decode_pattern = r'utf8_decode\s*\((.*?)\)'
    
    new_content, count_e = re.subn(encode_pattern, r"mb_convert_encoding(\1, 'UTF-8', 'ISO-8859-1')", content)
    new_content, count_d = re.subn(decode_pattern, r"mb_convert_encoding(\1, 'ISO-8859-1', 'UTF-8')", new_content)
    
    total = count_e + count_d
    if total > 0:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        return total
    return 0

def main():
    root_dir = r'c:\Users\3D Partner\Documents\OpenXe\OpenXE'
    exclude_dirs = {'vendor', '.git'}
    total_fixed = 0
    files_processed = 0

    for root, dirs, files in os.walk(root_dir):
        dirs[:] = [d for d in dirs if d not in exclude_dirs]
        for file in files:
            if file.endswith('.php'):
                files_processed += 1
                count = fix_utf8_functions(os.path.join(root, file))
                if count > 0:
                    total_fixed += count
                    print(f"Fixed {count} utf8 functions in {os.path.join(root, file)}")

    print(f"Total fixed: {total_fixed} in {files_processed} files.")

if __name__ == "__main__":
    main()
