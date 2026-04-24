"use client";

import { useState } from "react";
import EmojiPickerReact, { EmojiClickData, Theme } from "emoji-picker-react";
import { Popover, PopoverContent, PopoverTrigger } from "./popover";
import { Button } from "./button";
import { Smile } from "lucide-react";

export interface EmojiPickerProps {
  onEmojiSelect: (emoji: string) => void;
  disabled?: boolean;
}

export function EmojiPicker({ onEmojiSelect, disabled }: EmojiPickerProps) {
  const [open, setOpen] = useState(false);

  const handleEmojiClick = (emojiData: EmojiClickData) => {
    onEmojiSelect(emojiData.emoji);
    setOpen(false);
  };

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger>
        <Button
          variant="outline"
          size="icon"
          disabled={disabled}
          type="button"
          className="h-9 w-9"
        >
          <Smile className="h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-auto p-0" align="end">
        <EmojiPickerReact
          onEmojiClick={handleEmojiClick}
          theme={Theme.LIGHT}
          lazyLoadEmojis
          searchPlaceholder="Search emoji..."
        />
      </PopoverContent>
    </Popover>
  );
}
