import os
import re

controllers_dir = '/Users/apple/Herd/projects/ims/app/Http/Controllers'

# Regex to match the assignment of $isRegionalAdmin
# It captures the variable name used ($user or $currentUser)
pattern = re.compile(r'\$isRegionalAdmin\s*=\s*\$([a-zA-Z0-9_]+)->(?:region_id|hasRole).*?;')

for root, dirs, files in os.walk(controllers_dir):
    for file in files:
        if file.endswith('.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r') as f:
                content = f.read()
            
            new_content, count = pattern.subn(r'$isRegionalAdmin = $\1->region_id && $\1->hasRole(\'rao\');', content)
            
            if count > 0:
                with open(filepath, 'w') as f:
                    f.write(new_content)
                print(f"Updated {count} occurrences in {file}")

print("Done.")
