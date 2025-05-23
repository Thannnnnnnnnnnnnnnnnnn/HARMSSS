a = float(input("TECNO: "))
b = float(input("BPM: "))
c = float(input("ITSP2: "))
d = float(input("SA101: ")) 
e = float(input("SP101: "))
f = float(input("IAS102: "))


avg = (a + b + c + d + e + f) / 6
print("Average: " + str(avg))

if avg >= 1.00:
    print("Your average is: " + str(avg) + " Ang galing mo kupal")
elif avg >= 1.25:
    print("Your average is: " + str(avg) + " Galing mo boiiii")
elif avg >= 1.50:
    print("Your average is: " + str(avg) + " Pwede na tanga ka kasi")
elif avg >= 3.01:
    print("Your average is: " + str(avg) + " Wag kana mag aral mama mo si gamba kingina mo")