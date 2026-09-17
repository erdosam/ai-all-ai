package myclass

// Go has no public/protected/private keywords; exported (capitalized) vs.
// unexported (lowercase) identifiers are the equivalent of public/private.
// Member order: exported fields, unexported fields, constructor,
// exported methods, unexported methods.
type MyClass struct {
	Attribute1 string
	attribute2 string
}

func NewMyClass() *MyClass {
	return &MyClass{}
}

func (m *MyClass) DoOne() {
}

func (m *MyClass) doTwo() {
}
