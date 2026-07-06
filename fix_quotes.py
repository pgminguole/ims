import os

controllers_dir = '/Users/apple/Herd/projects/ims/app/Http/Controllers'

for root, dirs, files in os.walk(controllers_dir):
    for file in files:
        if file.endswith('.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r') as f:
                content = f.read()
            
            new_content = content.replace(r"\'rao\'", "'rao'")
            
            if new_content != content:
                with open(filepath, 'w') as f:
                    f.write(new_content)
                print(f"Fixed quotes in {file}")

print("Done.")
