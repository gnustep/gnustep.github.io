#!/usr/bin/mzscheme -qr

; You need the SSAX.sf.net library for MzScheme
; Copyright 2004 MJ Ray
; Released under the GNU GPL

(require "xhtmlfile.ss" "sxmlout.ss")

(define (main? ele)
  (and (list? ele)
       (not (null? ele))
       (equal? (car ele) 'div)
       (list? (cdr ele))
       (not (null? (cdr ele)))
       (list? (cadr ele))
       (member '(id "main") (cadr ele))))

(define (title? ele)
  (and (list? ele)
       (not (null? ele))
       (equal? (car ele) 'title)))

(define (tag-pick debug test tree)
  ;(display tree) (newline) (newline)
  (cond
    ((not (list? tree)) #f) ; you what?
    ((null? tree) #f) ; out of items
    ((and (list? tree)
          (not (null? (car tree)))
          (test (car tree)))
       (display (format "Picked ~a!\n" debug))
       (car tree)) ; found it
    ((and (list? (car tree))
          (not (null? (car tree)))
          (list? (cdar tree)))
       (tag-pick debug test (append (car tree) (cdr tree))))
    (#t (tag-pick debug test (cdr tree))))) ; not found it

(define (title-main-swap tree title ele)
  (define (title-main-swap-h tree)
    (cond
      ((main? tree) (display "Found main!\n") ele)
      ((not (list? tree)) tree)
      ((and (not (null? tree)) (equal? 'title (car tree))) (display "Found title!\n") (list 'title title))
      (#t (map title-main-swap-h tree))))
  (title-main-swap-h tree))

(define tpl (caddr (xhtmlfile (vector-ref argv 1))))
(define inp (xhtmlfile (vector-ref argv 0)))

(call-with-output-file
  (vector-ref argv 2)
  (lambda (p)
    (sxmlout
      (let ((main (tag-pick 'main main? inp))
            (title (tag-pick 'title title? inp)))
        (if main 
          (title-main-swap
            tpl
            (cadr (or title '(title "Untitled")))
            main)
          inp))
      p)))
