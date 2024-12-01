import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import ReactQuill from 'react-quill'
import 'react-quill/dist/quill.snow.css'
import { Button } from "../components/ui/button"
import { Input } from "../components/ui/input"
import { Label } from "../components/ui/label"
import { Textarea } from "../components/ui/textarea"
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "../components/ui/card"
import TextEditor from '../components/TextEditor'
import { Loader } from 'lucide-react'

const CreateBlog = () => {
  const [title, setTitle] = useState('')
  const [description, setDescription] = useState('')
  const [content, setContent] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()

  const handleSubmit = async (e) => {
    e.preventDefault()
    
    // Here you would typically make an API call to your server
    // For this example, we'll just log the data
 setLoading(true)
      fetch('http://localhost:8000/api/blogs', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title, description, content }),
      }).then((response) => {
        return response.json()
      }).then((data) => {
        setLoading(false)
        navigate('/edit-blog/'+data.blog.id)
          console.log(data);
          setTitle('')
          setDescription('')
          setContent('')
          
        
      })

    
   
    // Simulate an API call

    // Redirect to the blogs page after submission
  }

  return (
    <div className="max-w-4xl mx-auto">
      <Card>
        <CardHeader>
          <CardTitle>Create a New Blog Post</CardTitle>
        </CardHeader>
        <form onSubmit={handleSubmit}>
          <CardContent className="space-y-4 mb-12">
            <div className="space-y-2">
              <Label htmlFor="title">Title</Label>
              <Input
                id="title"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="description">Description</Label>
              <Textarea
                id="description"
                value={description}
                onChange={(e) => setDescription(e.target.value)}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="content">Content</Label>
             <TextEditor defaulteValue={content} onChange={setContent}/>
            </div>
          </CardContent>
          <CardFooter>
            <Button type="submit" className="w-full">{loading ? <p><Loader className='animate-spin' /> Creating...</p>: 'Create Blog Post'}</Button>
          </CardFooter>
        </form>
      </Card>
    </div>
  )
}

export default CreateBlog

