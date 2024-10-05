# Library Management System

givenbooks = []
books = []

def add_book():
    name = input('Enter book names (separated by commas):\n📖---> ')
    book_names = name.split(',')
    books.extend(book_names)
    print('Books added to the library successfully. ✅\n')

def show_books():
    if books:
        for i, book in enumerate(books, 1):
            print(f"{i}. {book}")
    else:
        print("There are no books in the library.")

def delete_book():
    if books:
        print('Enter the book number to be deleted from this list:\n')
        show_books()
        try:
            dele = int(input("--> ")) - 1
            if 0 <= dele < len(books):
                books.remove(books[dele])
                print('Book deleted successfully.')
            else:
                print('Invalid book number.')
        except ValueError:
            print('Please enter a valid number.')
    else:
        print("There are no books to delete.")

def rent_book():
    if books:
        gbook = {"name": '', "books": ''}
        gbook['name'] = input('Enter your name: ')
        
        print('Enter one book number to rent from this list:\n')
        show_books()
        
        try:
            rent = int(input("--> ")) - 1
            if 0 <= rent < len(books):
                gbook["books"] = books[rent]
                givenbooks.append(gbook)
                books.remove(books[rent])
                print("Book rented successfully.")
            else:
                print('Invalid input.')
        except ValueError:
            print('Please enter a valid number.')
    else:
        print("No books available to rent.")

def show_rented_books():
    if givenbooks:
        print("Rented Books:")
        for gbook in givenbooks:
            print(f"\nBook name: {gbook['books']}\nName of holder: {gbook['name']}")
    else:
        print("No books have been rented yet.")

def del_rent_list():
    if givenbooks:
        name = input("Enter the name of the book holder: ")
        b = input("Enter book name: ")
        for gbook in givenbooks:
            if name.lower() == gbook['name'].lower() and b.lower() == gbook['books'].lower():
                givenbooks.remove(gbook)
                print(f"Name {name} deleted from rent list.")
                return
        print(f"Name {name} not found in rent list.")
    else:
        print("No rented books to delete.")

def edit_rent():
    if givenbooks:
        name = input("Enter book holder's name: ")
        bkname = input("Enter current book name: ")
        for gbook in givenbooks:
            if name.lower() == gbook['name'].lower() and bkname.lower() == gbook['books'].lower():
                print('Enter one book number to update rent list:\n')
                show_books()
                
                try:
                    rent = int(input("--> ")) - 1
                    if 0 <= rent < len(books):
                        books.append(bkname)
                        gbook["books"] = books[rent]
                        books.remove(books[rent])
                        print("Rent list updated successfully.")
                        return
                except ValueError:
                    print('Please enter a valid number.')
        print(f"Name {name} and book {bkname} not found in rent list.")
    else:
        print("No rented books to edit.")

def exit_program():
    print("\nExiting....\n------------------------\nThank you\n------------------------")

# Main loop
option = 0
while option != 8:
    print("\n------------------------\n1). Add book\n2). Show books\n3). Delete book\n4). Rent a book\n5). Show rented books\n6). Delete rent list\n7). Edit rent list\n8). Exit")
    print('------------------------\n')
    
    try:
        option = int(input('Enter option\n--> '))
        if option == 1:
            add_book()
        elif option == 2:
            show_books()
        elif option == 3:
            delete_book()
        elif option == 4:
            rent_book()
        elif option == 5:
            show_rented_books()
        elif option == 6:
            del_rent_list()
        elif option == 7:
            edit_rent()
        elif option == 8:
            exit_program()
        else:
            print('Invalid input. Please enter a number from the options.')
    except ValueError:
        print('Please enter a valid number.')
