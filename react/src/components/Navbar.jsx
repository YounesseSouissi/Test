import React from 'react'
import { Button } from "./ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu"
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar"
import { Menu } from 'lucide-react'
import { Link } from 'react-router-dom'

const Navbar= () => {
  const [isOpen, setIsOpen] = React.useState(false)

  const handleLogout = () => {
    // Implement logout logic here
    console.log('Logging out...')
  }

  return (
    <nav className="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 dark:border-border">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          <div className='flex items-center '>
          <div className="flex items-center mr-4">
            <Link to="/" className="text-xl font-bold">
              Logo
            </Link>
          </div>
          <div className="hidden md:block">
            <div className="flex items-center gap-4 text-sm xl:gap-6" >
              <Link to="/blogs" className="hover:bg-primary-foreground hover:text-primary px-3 py-2 rounded-md text-sm font-medium">
                Blogs
              </Link>
              <Link to="/create-blog" className="hover:bg-primary-foreground hover:text-primary px-3 py-2 rounded-md text-sm font-medium">
                Create Blog
              </Link>
            </div>
          </div>

          </div>
          <div className="hidden md:block">
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="relative h-8 w-8 rounded-full">
                  <Avatar className="h-8 w-8">
                    <AvatarImage src="/avatars/01.png" alt="@username" />
                    <AvatarFallback>UN</AvatarFallback>
                  </Avatar>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent className="w-56" align="end" forceMount>
                <DropdownMenuItem onClick={handleLogout}>
                  Log out
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
          <div className="md:hidden">
            <Button variant="ghost" onClick={() => setIsOpen(!isOpen)}>
              <Menu className="h-6 w-6" />
            </Button>
          </div>
        </div>
      </div>
      {isOpen && (
        <div className="md:hidden">
          <div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <Link to="/blogs" className="hover:bg-primary-foreground hover:text-primary block px-3 py-2 rounded-md text-base font-medium">
              Blogs
            </Link>
            <Link to="/create-blog" className="hover:bg-primary-foreground hover:text-primary block px-3 py-2 rounded-md text-base font-medium">
              Create Blog
            </Link>
            <Button variant="ghost" className="w-full text-left" onClick={handleLogout}>
              Log out
            </Button>
          </div>
        </div>
      )}
    </nav>
  )
}

export default Navbar

