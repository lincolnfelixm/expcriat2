import hashlib

def h(v):
    s = hashlib.sha512()
    s.update(v.encode('utf-8'))
    return s.hexdigest()

def f(n):
    seq = [0, 1]
    for i in range(2, n):
        seq.append(seq[i-1] + seq[i-2])
    return seq

def hl(filename, idx):
    hashes = []
    with open(filename, 'r') as file:
        content = file.read()
        for i in idx:
            if i < len(content):
                l = content[i]
                h_l = h(l)
                hashes.append(h_l)
    return hashes

def ch(hashes):
    chash = h(''.join(hashes))
    return chash

def main():
    filename = 'test.txt'
    num = 64

    fib = f(num)
    idx = set(fib)

    letter_hashes = hl(filename, idx)

    c_hash = ch(letter_hashes)

    # print("\nCombined Hash:")
    # print(c_hash)
    return c_hash

main()
