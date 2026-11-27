import os
import re

def fix_nullable_params(file_path):
    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    # Pattern to find function declarations with implicitly nullable parameters
    # Matches: Type $var = null
    # Excludes: ?Type $var = null
    
    pattern = r'(\(|,\s*)(?<!\?)\b(string|int|float|bool|array|object|callable|iterable|resource|[A-Z][a-zA-Z0-9_]*)\s+\$([a-zA-Z0-9_]+)\s*=\s*null'
    
    # We need to use a function for replacement to handle the lookbehind limitation in re module
    # or just use a group that includes the preceding character.
    # The pattern above uses (\(|,\s*) to match the start.
    
    new_content, count = re.subn(pattern, r'\1?\2 $\3 = null', content)
    
    if count > 0:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        return count
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
                count = fix_nullable_params(os.path.join(root, file))
                if count > 0:
                    total_fixed += count
                    print(f"Fixed {count} in {os.path.join(root, file)}")

    print(f"Total fixed: {total_fixed} in {files_processed} files.")

if __name__ == "__main__":
    main()
